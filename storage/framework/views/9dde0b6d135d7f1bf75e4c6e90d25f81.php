<?php $__env->startComponent($typeForm, get_defined_vars()); ?>
    <div data-controller="select"
        data-select-placeholder="<?php echo e($attributes['placeholder'] ?? ''); ?>"
        data-select-allow-empty="<?php echo e($allowEmpty); ?>"
        data-select-message-notfound="<?php echo e(__('No results found')); ?>"
        data-select-allow-add="<?php echo e(var_export($allowAdd, true)); ?>"
        data-select-message-add="<?php echo e(__('Add')); ?>"
    >
        <select <?php echo e($attributes); ?>>
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>"
                        <?php if(isset($value)): ?>
                            <?php if(is_array($value) && in_array($key, $value)): ?> selected
                            <?php elseif(isset($value[$key]) && $value[$key] == $option): ?> selected
                            <?php elseif($key == $value): ?> selected
                            <?php endif; ?>
                        <?php endif; ?>
                ><?php echo e($option); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\orchid\vendor\orchid\platform\resources\views/fields/select.blade.php ENDPATH**/ ?>