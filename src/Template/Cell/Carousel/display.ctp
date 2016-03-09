<?php
$carouselId = 'carousel-' . $alias; ?>
<div id="<?= $carouselId ?>" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <?php foreach ($slides as $key => $slide) : ?>
        <li data-target="<?= $alias . '-' . $key ?>" data-slide-to="<?= $key ?>" class="<?php echo (!$key) ? 'active' : ''; ?>"></li>
    <?php endforeach; ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <?php foreach ($slides as $key => $slide) : ?>
        <div <?= ($slide->identifier) ? 'id="' . $slide->identifier . '"' : ''; ?>  class="item <?= (!$key) ? 'active' : ''; ?> <?= $slide->class ?>">
            <?php
            if (!$slide->link) :
                echo $this->Html->image($slide->img_src, [
                    'alt' => $slide->caption,
                ]);
            else :
                echo $this->Html->link(
                    $this->Html->image($slide->img_src, ['alt' => $slide->caption]),
                    $slide->link,
                    ['escape' => false]
                );
            endif; ?>
            <div class="carousel-caption"><?= $slide->caption; ?></div>
        </div>
    <?php endforeach; ?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#<?= $carouselId ?>" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#<?= $carouselId ?>" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>