<?php

// Let's make things easier
$ext_instance = fw()->extensions->get( 'woffice-time-tracking' );

$log = $ext_instance->getLog(get_current_user_id());

$class = (woffice_tracking_is_working()) ? 'is-tracking' : '';

echo $before_widget; ?>

<!-- WIDGET -->
<div class="woffice-time-tracking <?php echo $class; ?>">

    <?php if(!is_user_logged_in()) : ?>

        <div class="woffice-time-tracking-head">
            <i class="fa fa-lock"></i>
            <div class="intern-box box-title">
                <h3><?php _e('Sorry ! It is only for logged users.','woffice'); ?></h3>
            </div>
        </div>

    <?php else: ?>

        <div class="woffice-time-tracking-head">
            <i class="fa fa-clock-o"></i>
            <?php if(!empty($title)) : ?>
                <div class="intern-box box-title">
                    <h3><?php echo $title; ?></h3>
                </div>
            <?php endif; ?>
        </div>

        <div class="woffice-time-tracking-content">
            <div class="woffice-time-tracking-view text-center">
                <p><?php echo $description; ?></p>
                <div class="woffice-time-tracking_time-displayed"><?php echo woffice_current_tracking_time(); ?></div>
            </div>
            <div class="woffice-time-tracking-view text-left" style="display: none;">
                <?php if(!empty($log)) : ?>
                    <?php foreach ($log as $day=>$hours) : ?>
                        <?php
                        $hours = abs($hours);
                        if($hours == 0)
                            continue;
                        ?>
                        <div class="woffice-time-tracking-day">
                            <span class="label"><?php echo $day; ?></span>
                            <span><?php echo sprintf(_n( '%s hour', '%s hours', $hours, 'woffice' ), $hours); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php _e('No tracks so far. Get started!', 'woffice'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="woffice-time-tracking-actions text-center">
            <a href="javascript:void(0);" class="woffice-time-tracking-history-toggle btn btn-default">
                <i class="fa-history fa"></i> <?php _e('Tracks', 'woffice'); ?>
            </a>
            <?php if(!woffice_tracking_is_working()) : ?>
                <a href="javascript:void(0);" class="woffice-time-tracking-state-toggle btn btn-default btn-info">
                    <i class="fa fa-play"></i> <span><?php _e('Start', 'woffice'); ?></span>
                </a>
            <?php else: ?>
                <a href="javascript:void(0);" class="woffice-time-tracking-state-toggle btn btn-default btn-danger">
                    <i class="fa fa-stop"></i> <span><?php _e('Stop', 'woffice'); ?></span>
                </a>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<?php echo $after_widget; ?>