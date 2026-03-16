<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?> - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="<?php echo e(asset('css/fonts.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0D3B66;
            --secondary-blue: #1E5B94;
            --accent-blue: #2A77C9;
            --light-gray: #F3F4F6;
            --text-gray: #6B7280;
            --border-color: #E5E7EB;
        }
        
        /* Ensure font consistency */
        body {
            font-family: var(--font-family-sans);
        }
        
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
            padding: 1.5rem;
        }
        .auth-container {
            max-width: 360px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(13, 59, 102, 0.05),
                       0 10px 15px -3px rgba(13, 59, 102, 0.1);
            position: relative;
            overflow: hidden;
        }
        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-blue));
        }
        .auth-inner {
            padding: 2rem;
        }
        .auth-logo {
            width: 80px;
            height: 80px;
            background: var(--primary-blue);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 4px 6px -1px rgba(13, 59, 102, 0.1);
            position: relative;
        }
        .auth-logo::after {
            content: '';
            position: absolute;
            inset: -3px;
            border: 2px solid var(--primary-blue);
            border-radius: 18px;
            opacity: 0.1;
        }
        .auth-heading {
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            color: var(--primary-blue);
            margin-bottom: 0.75rem;
        }
        .auth-subheading {
            font-size: 0.938rem;
            color: var(--text-gray);
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.938rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--light-gray);
            transition: all 0.2s;
        }
        .form-input:hover {
            border-color: var(--accent-blue);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-blue);
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(42, 119, 201, 0.1);
        }
        .form-input::placeholder {
            color: #9CA3AF;
        }
        .submit-button {
            width: 100%;
            padding: 0.875rem;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.938rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .submit-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, 
                rgba(255,255,255,0) 0%, 
                rgba(255,255,255,0.1) 50%, 
                rgba(255,255,255,0) 100%);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }
        .submit-button:hover {
            background: var(--secondary-blue);
        }
        .submit-button:hover::before {
            transform: translateX(100%);
        }
        @media (max-width: 640px) {
            .auth-wrapper {
                padding: 1rem;
            }
            .auth-inner {
                padding: 1.5rem;
            }
            .auth-logo {
                width: 64px;
                height: 64px;
            }
            .auth-heading {
                font-size: 1.5rem;
            }
            .auth-subheading {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-inner">
                <div class="auth-logo">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <h1 class="auth-heading">Welcome Back</h1>
                <p class="auth-subheading">Please sign in to your account</p>

                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="email" class="form-label">Email address</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Enter your email"
                               value="<?php echo e(old('email')); ?>" 
                               required 
                               autocomplete="email">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               class="form-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Enter your password"
                               required 
                               autocomplete="current-password">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <button type="submit" class="submit-button">
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/laravel-app/releases/11/resources/views/auth/login.blade.php ENDPATH**/ ?>