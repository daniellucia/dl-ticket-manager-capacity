<?php

namespace DL\TicketManagerCapacity;

defined('ABSPATH') || exit;

class Plugin
{

    public function init(): void
    {
        add_filter('manage_edit-product_columns', [$this, 'addTicketColumn']);
        add_action('manage_product_posts_custom_column', [$this, 'renderTicketColumn'], 10, 2);
        add_action('admin_head', [$this, 'customColumnStyle']);
        add_action('dl_ticket_event_fields_after', [$this, 'eventFieldsAfter']);
        add_action('dl_ticket_save_event_fields', [$this, 'saveEventFields']);

        add_filter('dl_ticket_purchasable', [$this, 'filterTicketPurchasable'], 10, 2);
    }

    /**
     * Guarda el aforo del evento cuando es enviado
     * @param mixed $post_id
     * @return void
     * @author Daniel Lucia
     */
    public function saveEventFields($post_id)
    {
        $capacity = isset($_POST['_event_capacity']) ? intval($_POST['_event_capacity']) : 0;
        update_post_meta($post_id, '_event_capacity', $capacity);
    }


    /**
     * Muestra el campo de aforo
     * @return void
     * @author Daniel Lucia
     */
    public function eventFieldsAfter()
    {

        echo '<div class="options_group">';
            woocommerce_wp_text_input([
                'id'  => '_event_capacity',
                'label'  => __('Event capacity', 'dl-ticket-manager-capacity'),
                'placeholder' => __('Capacity', 'dl-ticket-manager-capacity'),
                'desc_tip' => true,
                'description' => __('Capacity limit. Leave zero for indeterminate.', 'dl-ticket-manager-capacity'),
                'type'  => 'number',
                'custom_attributes' => [
                    'min' => '0',
                    'step' => '1'
                ],
            ]);
        echo '</div>';
    }

    /**
     * Evita la compra de tickets si el aforo es 0
     * @param bool $purchasable
     * @param WC_Product $product
     * @return bool
     * @author Daniel Lucia
     */
    public function filterTicketPurchasable(bool $purchasable, \WC_Product $product): bool
    {
        if ($product->is_type('ticket')) {

            //Verificamos aforo
            $capacity = $product->get_meta('_event_capacity');
            $sales = $product->get_total_sales();

            if ($capacity && $sales >= $capacity) {
                $purchasable = false;
            }
        }

        return $purchasable;
    }

    /**
     * Estilo a la columna de aforo
     * @return void
     * @author Daniel Lucia
     */
    public function customColumnStyle()
    {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'edit-product') {
            echo '<style>
                .wp-list-table th.column-event_capacity { width: 10%; }
            </style>';
        }
    }

    /**
     * Añade la columna de aforo a la tabla de productos
     * @param array $columns
     * @return array
     * @author Daniel Lucia
     */
    public function addTicketColumn(array $columns): array
    {
        $new_columns = [];
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key === 'name') {
                $new_columns['event_capacity'] = '';
            }
        }
        return $new_columns;
    }

    /**
     * Renderiza el contenido de la columna de aforo
     * @param string $column
     * @param int $post_id
     * @return void
     * @author Daniel Lucia
     */
    public function renderTicketColumn(string $column, int $post_id): void
    {
        if ($column !== 'event_capacity') {
            return;
        }

        $product = wc_get_product($post_id);
        if ($product && $product->is_type('ticket')) {

            $capacity = get_post_meta($post_id, '_event_capacity', true);
            if ((int)$capacity == 0) {
                echo '<span style="color:#0073aa; font-size:0.85em;">' . __('No allocated capacity', 'dl-ticket-manager-capacity') . '</span>';
            } else {
                $product = wc_get_product($post_id);
                $sales = $product->get_total_sales();
                echo '<span style="color:#0073aa; font-size:0.85em;">' . esc_html($sales) . ' / ' . esc_html($capacity) . '</span><br />';

                $percent = $capacity > 0 ? ($sales / $capacity) * 100 : 0;
                echo '<div style="overflow:hidden;position:relative;height: 5px;width: 100%;background-color: #f0f0f0;margin: 5px 0 0 0;border: 1px solid #0073aa;border-radius: 3px;">';
                echo '<div style="position:absolute;top:0;left:0;height:100%;width:' . esc_attr($percent) . '%;background-color:#0073aa;border-radius:3px;"></div>';
                echo '</div>';
            }
        }
    }
}
