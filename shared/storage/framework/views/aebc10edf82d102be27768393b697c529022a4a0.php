<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GST Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>GST Report</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>INV Date</th>
                <th>INV #</th>
                <th>Net Amount</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Total Amount</th>
                <th>Fullname</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>GST No</th>
                <th>City</th>
                <th>State</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($item->inv_date ? \Carbon\Carbon::parse($item->inv_date)->format('d/m/Y') : ''); ?></td>
                    <td><?php echo e($item->inv_no ?? ''); ?></td>
                    <td><?php echo e(number_format($item->net_amount ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($item->cgst ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($item->sgst ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($item->igst ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($item->total_amount ?? 0, 2)); ?></td>
                    <td><?php echo e($item->fullname ?? ''); ?></td>
                    <td><?php echo e($item->mobile ?? ''); ?></td>
                    <td><?php echo e(strtolower($item->email ?? '')); ?></td>
                    <td><?php echo e($item->gst_no ?? ''); ?></td>
                    <td><?php echo e($item->city ?? ''); ?></td>
                    <td><?php echo e($item->state ?? ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="14" style="text-align: center; padding: 20px;">No data available for the selected criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/laravel-app/releases/11/resources/views/exports/gst_report_pdf.blade.php ENDPATH**/ ?>