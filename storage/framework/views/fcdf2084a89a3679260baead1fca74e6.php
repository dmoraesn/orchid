<?php $__currentLoopData = $kanbanData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage => $leads): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = \Illuminate\Support\Arr::wrap($leads); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $lead ?? ''; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\orchid\vendor\orchid\platform\resources\views/layouts/blank.blade.php ENDPATH**/ ?>