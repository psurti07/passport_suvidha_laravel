<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customers Export</title>
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
        .status-paid {
            color: #0f766e;
        }
        .status-lead {
            color: #dc2626;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Customers Report</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Pack Code</th>
                <th>Address</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>POB</th>
                <th>Nationality</th>
                <th>Service Code</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($customer->id); ?></td>
                    <td><?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?></td>
                    <td><?php echo e($customer->email); ?></td>
                    <td><?php echo e($customer->mobile_number); ?></td>
                    <td class="<?php echo e($customer->is_paid ? 'status-paid' : 'status-lead'); ?>">
                        <?php echo e($customer->is_paid ? 'Paid' : 'Lead'); ?>

                    </td>
                    <td><?php echo e($customer->created_at->format('Y-m-d H:i')); ?></td>
                    <td><?php echo e($customer->pack_code); ?></td>
                    <td><?php echo e($customer->address); ?></td>
                    <td><?php echo e($customer->gender); ?></td>
                    <td><?php echo e($customer->date_of_birth ? date('Y-m-d', strtotime($customer->date_of_birth)) : ''); ?></td>
                    <td><?php echo e($customer->place_of_birth); ?></td>
                    <td><?php echo e($customer->nationality); ?></td>
                    <td><?php echo e($customer->service_code); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="13" style="text-align: center;">No customers found for the selected criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html> <?php /**PATH /var/www/laravel-app/releases/11/resources/views/exports/customers.blade.php ENDPATH**/ ?>