<?php

class TMCapacityManagementPlugin
{

    public function init(): void
    {
        add_filter('manage_edit-product_columns', [$this, 'addTicketColumn']);
        add_action('manage_product_posts_custom_column', [$this, 'renderTicketColumn'], 10, 2);
        add_action('admin_head', [$this, 'customColumnStyle']);
    }

    public function customColumnStyle()
    {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'edit-product') {
            echo '<style>
                .wp-list-table th.column-event_capacity { width: 10%; }
            </style>';
        }
    }

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
