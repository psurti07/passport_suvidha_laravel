<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OTP Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .verified {
            color: green;
        }
        .pending {
            color: orange;
        }
    </style>
</head>
<body>
    <h2>OTP Report</h2>
    <p>Generated on: <?php echo e(date('Y-m-d H:i:s')); ?></p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mobile Number</th>
                <th>OTP</th>
                <th>Sent At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $otps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $otp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($otp->id); ?></td>
                <td><?php echo e($otp->mobile_number); ?></td>
                <td><?php echo e($otp->otp); ?></td>
                <td><?php echo e($otp->sent_at->format('Y-m-d H:i:s')); ?></td>
                <td class="<?php echo e($otp->is_verified ? 'verified' : 'pending'); ?>">
                    <?php echo e($otp->is_verified ? 'Verified' : 'Pending'); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" style="text-align: center;">No OTPs found for the selected criteria.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html> <?php /**PATH /var/www/laravel-app/releases/11/resources/views/admin/otps/pdf.blade.php ENDPATH**/ ?>