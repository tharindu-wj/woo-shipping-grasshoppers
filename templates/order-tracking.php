<?php
/**
 * Template file for order tracking details
 */
?>
<div class="order-tracking">
    <h2>Order Tracking</h2>
    <?php if (!empty($tracking_data)): ?>
        <table class="woocommerce-table woocommerce-order-tracking">
            <thead>
            <tr>
                <th>REMARK</th>
                <th>TIMESTAMP</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tracking_data as $hub): ?>
                <tr>
                    <td><?php echo $hub['REMARK']; ?></td>
                    <td><?php echo $hub['TIMESTAMP']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tracking information not found for this order.</p>
    <?php endif; ?>
</div>
