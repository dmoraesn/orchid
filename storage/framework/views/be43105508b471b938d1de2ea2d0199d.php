<div class="form-group">
    <?php if(isset($title)): ?>
        <label for="<?php echo e($id); ?>" class="form-label"><?php echo e($title); ?>

            <?php if(isset($attributes['required']) && $attributes['required']): ?>
                <sup class="text-danger">*</sup>
            <?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal1d1976506f33d5d23fa37b3ec2628c63 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d1976506f33d5d23fa37b3ec2628c63 = $attributes; } ?>
<?php $component = Orchid\Screen\Components\Popover::resolve(['content' => $popover ?? ''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('orchid-popover'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Orchid\Screen\Components\Popover::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d1976506f33d5d23fa37b3ec2628c63)): ?>
<?php $attributes = $__attributesOriginal1d1976506f33d5d23fa37b3ec2628c63; ?>
<?php unset($__attributesOriginal1d1976506f33d5d23fa37b3ec2628c63); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d1976506f33d5d23fa37b3ec2628c63)): ?>
<?php $component = $__componentOriginal1d1976506f33d5d23fa37b3ec2628c63; ?>
<?php unset($__componentOriginal1d1976506f33d5d23fa37b3ec2628c63); ?>
<?php endif; ?>
        </label>
    <?php endif; ?>

    <?php echo e($slot); ?>


    <?php
        // Backport for consistent error handling behavior between Laravel 10 and 11.
        // This implementation will be modified in a future major version.

        // Retrieve all errors from the $errors object and convert them into a collection
        $allErrors = collect($errors->all());

        // Check if there is a 'default' error key in the collection of errors
        if ($allErrors->has('default')) {
            // If a 'default' error exists, assign it to the $errors variable
            $errors = $allErrors->get('default');
        }
    ?>

    <?php if($errors->has($oldName)): ?>
        <div class="invalid-feedback d-block">
            <small><?php echo e($errors->first($oldName)); ?></small>
        </div>
    <?php elseif(isset($help)): ?>
        <small class="form-text text-muted"><?php echo $help; ?></small>
    <?php endif; ?>
</div>

<?php if(isset($hr)): ?>
    <div class="line line-dashed border-bottom my-3"></div>
<?php endif; ?>
<?php /**PATH C:\orchid\vendor\orchid\platform\resources\views/partials/fields/vertical.blade.php ENDPATH**/ ?>