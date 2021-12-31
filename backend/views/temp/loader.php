<?php
$theme_loader = (isset($theme_loader))?$theme_loader:"fix";
$loader = (isset($loader))?$loader:"loader";
$display = (isset($display))?$display:"none";
?>
<div class="theme_loader_<?=$theme_loader?> <?=$loader?>" style="display: <?=$display?>;">
    <div class="cell preloader5 loader-block divbox">
        <div class="circle-5 l"></div>
        <div class="circle-5 m"></div>
        <div class="circle-5 r"></div>
    </div>
</div>
