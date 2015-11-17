<?

include_once APPPATH . 'libraries/recurlyaccess.php';
include_once APPPATH . 'libraries/Date_Difference.phps';
?>
<style type="text/css">
    #capacitymeter { margin-bottom:20px; }
    #capacitymeter .label { float:left; }
    #capacitymeter .meter { background-color:#999999; height:25px; margin:0 10px 0 0; float:left; }
    #capacitymeter .meter .amount { background-color:#66FFCC; height:20px; }
    #capacitymeter .numeric { float:left; }
</style>
<div id="dashboard">

  <aside id="sidebar">
    <section id="member-search">
		<?=g_form('admin/usermanagement/search','post');?>
          <input type="search" value="Search for a Member" class="field" name="q" id="q" autocomplete="off" />
      </form>
      <br />
      <br />
      <br />
      <br />
      <p id="register"><b><?=g_anchor("admin/usermanagement/user", "Register a New Member") ?></b></p>
      <p id="register"><b><?=g_anchor("admin/usermanagement/invite", "Invite a New Member") ?></b></p>
    </section>
    
    <section id="conference-rooms">
      <h3>Conference Rooms</h3>
      <ul>
        <? foreach($location->conferencerooms as $room): ?>
          <li><a href="<?=$room->calendar_link?>" target="_blank"><?=$room->space_name?></a></li>
        <? endforeach; ?>
      </ul>
    </section>
    
    
    <section id="access-pricing">
      <h3>Access Pricing</h3>
		<?= g_form('admin/locationmanagement/updateaccesspricing')?>
        <div class="fieldgroup">
          <ul>
            <li>
              <div class="label"><?=form_label("Daily rate", "daily_rate")?></div>
              <div class="field"><?=form_input(array("type" => "number", "step" => ".05", "min" => "0.00", "id" => "daily_rate", "name" => "daily_rate", "value" => (isset($accesspricing) ? $accesspricing->daily_rate : "0"))); ?></div>
            </li>
            <li>
                <?
                $planOptions = array();
                foreach ($plans as $plan) {
					if ($plan->plan_code != "daily"){
                    $planOptions[$plan->plan_code] = $plan->name;   
					}
                };
                ?>
              <div class="label"><?=form_label("Default monthly rate code", "default_monthly_rate_code")?></div>
              <div class="field"><?=form_dropdown("default_monthly_rate_code", $planOptions, (isset($accesspricing) ? $accesspricing->default_monthly_rate_code : ""), 'id = "default_monthly_rate_code" name = "default_monthly_rate_code"') ?></div>
            </li>
            <li>
              <?=form_hidden("allow_monthly_memberships", "0") . form_checkbox(array("id" => "allow_monthly_memberships", "name" => "allow_monthly_memberships", "value" => "1", "checked" => (isset($accesspricing) ? $accesspricing->allow_monthly_memberships : 0))); ?>
              <?=form_label("Allow Monthly Membership Sign-up", "allow_monthly_membership")?>
            </li>
            <li>
              <?=form_submit("btnSubmit", "Update") ?>
            </li>
          <ul>
        </div>
      </form>
    </section>
    
  </aside><!-- end #sidebar -->
  

  <section id="checked-in" class="module">
    <h2>Checked In <span class="right"><?=count($signedInMembers)?> / <?=$location->capacity?></span></h2>
    <table width="100%" class="list">
      <? foreach($signedInMembers as $user): ?>
        <tr>
          <td class="name"><?=g_anchor("admin/usermanagement/viewprofile/" . $user->id, $user->first_name ." ". $user->last_name) ?><br /><? if($user->company != "") { echo "<span class=\"company\">" . $user->company . "</span>"; } ?>
          </td>
          <td><?=$user->plan_code?></td>
          <td class="date"><?=format_date($user->sign_in,true) ?></td>
        </tr>
      <? endforeach; ?>
    </table>
    <p class="see-all"><b><?=g_anchor("admin/locationmanagement/whoshere/" . $location->id, "See All")?></b></p>
  </section>
  
  <section id="issues" class="module">
    <h2>Issues</h2>
    <table width="100%">
      <? foreach ($issues as $issue): ?>
        <tr>
          <td class="issue_type"><?=$issue->type ?></td>
          <td class="user_name"><?=$issue->user_name ?></td>
          <td class="message"><?=$issue->message ?></td>
          <td class="date"><?=format_date($issue->date,true) ?></td>
        </tr>
      <? endforeach; ?>
    </table>
    <p class="see-all"><b><?=g_anchor("admin/issuesmanagement/index", "See All");?></b></p>
  </section>
    
    <!--
    <div id="confrooms" class="module">
        div class="spaces">
            <ul>
            <? foreach($location->spaces as $space): ?>
                <li class="space">
                    <?=g_anchor("/admin/locationmanagement/locationspace/" . $location->id . "/" . $space->id, $space->name) ?>
                    &nbsp;<?=($space->is_bookable ? g_anchor_popup($space->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>
                    <? if ($space->spaces):?> 
                        <ul>
                        <? foreach($space->spaces as $subspace1): ?>
                            <li class="space">
                                <?=g_anchor("/admin/locationmanagement/locationspace/" . $location->id . "/" . $subspace1->id, $subspace1->name) ?>
                                &nbsp;<?=($subspace1->is_bookable ? g_anchor_popup($subspace1->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>
        
                                <? if ($subspace1->spaces):?> 
                                    <ul>
                                    <? foreach($subspace1->spaces as $subspace2): ?>
                                        <li class="space">
                                            <?=g_anchor("/admin/locationmanagement/locationspace/" . $location->id . "/" . $subspace2->id, $subspace2->name) ?>
                                            &nbsp;<?=($subspace2->is_bookable ? g_anchor_popup($subspace2->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>
        
                                            <? if ($subspace2->spaces): ?>
        
                                                <ul>
                                                <? foreach($subspace2->spaces as $subspace3): ?>
                                                    <li class="space">
                                                        <?=g_anchor("/admin/locationmanagement/locationspace/" . $location->id . "/" . $subspace3->id, $subspace3->name) ?>
                                                        &nbsp;<?=($subspace3->is_bookable ? g_anchor_popup($subspace3->calendar_link, img("/images/calendaricon.jpg")) : "" ) ?>
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
    </div>
    -->

</div><!-- end #dashboard -->