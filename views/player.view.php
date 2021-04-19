<?php if(isWatch()): ?>
    <button class="uk-button uk-button-default uk-margin-medium-bottom" onclick="window.history.back();"><i class="uk-icon-angle-double-left"></i> <?php echo _GOBACK ?></button>
    <?php if(isLogged()): ?>


        <div class="uk-cover uk-margin-top uk-margin-bottom">
            <video id="player" playsinline controls>

                <?php if(isWatch() && !isEpisode()): ?>
                <source src="<?php echo echoOutput($itemDetails['link']); ?>" type="video/mp4" />
                <?php endif; ?>

                <?php if(isWatch() && isEpisode()): ?>
                <source src="<?php echo echoOutput($episodeDetails['episode_link']); ?>" type="video/mp4" />
                <?php endif; ?>

            </video>
        </div>

    <?php endif; ?>

    <?php if (!isLogged()): ?>

        <div class="uk-margin-large-top">
            <a class="uk-text-muted" href="<?php echo $urlPath->signin(); ?>">
            <div class="uk-box-border">
                <?php echo _PLEASESIGNINTOWATCH ?>
                <i class="ion-ios-arrow-right uk-float-right"></i>
            </div>
        </a>
        </div>

    <?php endif; ?>

    <?php endif; ?>