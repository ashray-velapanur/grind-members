<?
/**
 * listing of issues view template
 * 
 * returns a list of issues formatted in a table
 *
 * @joshcampbell
 * @view template
 */
 
  include_once APPPATH . 'libraries/Date_Difference.phps';

 
 ?>

<section id="issues" class="module">
  <h2>Issues</h2>
  <table width="100%">
    <? foreach ($issues as $issue): ?>
      <tr>
        <td class="issue_type"><?=$issue->type ?></td>
        <td class="user_name"><?=$issue->user_name ?></td>
        <td class="message"><?=$issue->message ?></td>
        <td class="date"><?=Date_Difference::getString(new DateTime($issue->date)) ?></td>
      </tr>
    <? endforeach; ?>
  </table>
  <p class="see-all"><b><?=g_anchor("/admin/issuesmanagement/index", "See All");?></b></p>
