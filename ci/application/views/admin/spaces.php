<div id="globalactions">
    [<?=g_anchor("admin/locationmanagement/locationspace/" . $location->id, "Add Space")?>]
</div>

<div class="spaces">
    <ul>
    <? foreach($location->spaces as $space): ?>
        <li class="space">
            <?=g_anchor("admin/locationmanagement/locationspace/" . $location->id . "/" . $space->id, $space->name) ?>
            &nbsp;<?=($space->is_bookable ? anchor_popup($space->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>
            <? if ($space->spaces):?> 
                <ul>
                <? foreach($space->spaces as $subspace1): ?>
                    <li class="space">
                        <?=g_anchor("admin/locationmanagement/locationspace/" . $location->id . "/" . $subspace1->id, $subspace1->name) ?>
                        &nbsp;<?=($subspace1->is_bookable ? anchor_popup($subspace1->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>

                        <? if ($subspace1->spaces):?> 
                            <ul>
                            <? foreach($subspace1->spaces as $subspace2): ?>
                                <li class="space">
                                    <?=g_anchor("admin/locationmanagement/locationspace/" . $location->id . "/" . $subspace2->id, $subspace2->name) ?>
                                    &nbsp;<?=($subspace2->is_bookable ? anchor_popup($subspace2->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>

                                    <? if ($subspace2->spaces): ?>

                                        <ul>
                                        <? foreach($subspace2->spaces as $subspace3): ?>
                                            <li class="space">
                                                <?=g_anchor("admin/locationmanagement/locationspace/" . $location->id . "/" . $subspace3->id, $subspace3->name) ?>
                                                &nbsp;<?=($subspace3->is_bookable ? anchor_popup($subspace3->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>
                                                <? if ($subspace3->spaces): ?>
                                                    <? // could hardcode another level in here... ?>
                                                <? endif; ?>
                                            </li>
                                        <? endforeach; ?>
                                        </ul>

                                    <? endif; ?>
                                </li>
                            <? endforeach; ?>
                            </ul>
                        <? endif; ?>

                    </li>
                <? endforeach; ?>
                </ul>
            <? endif; ?>
        </li>
    <? endforeach; ?>
    </ul>
</div>
