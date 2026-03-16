<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users List</title>
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
        .status-active {
            color: #0f766e;
        }
        .status-inactive {
            color: #dc2626;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Users List</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($user->id); ?></td>
                    <td><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                    <td><?php echo e($user->name); ?></td>
                    <td><?php echo e($user->email); ?></td>
                    <td class="<?php echo e($user->is_active ? 'status-active' : 'status-inactive'); ?>">
                        <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                    </td>
                    <td><?php echo e($user->creator->name ?? 'N/A'); ?></td>
                    <td><?php echo e($user->created_at->format('d/m/Y H:i')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html> <?php /**PATH /var/www/laravel-app/releases/11/resources/views/exports/users.blade.php ENDPATH**/ ?>