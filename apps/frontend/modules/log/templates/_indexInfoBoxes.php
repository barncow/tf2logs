<div id="infoBoxContainer">
  <div id="topViewed" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Top Viewed Logs Added in Past Week</div>
    <div class="content">
      <table width="100%">
      <?php foreach($topViewedLogs as $l): ?>
        <tr><td><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></td><td style="white-space: nowrap;"><?php echo $l->getViews() ?></td></tr>
      <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>

  <div id="topUploaders" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Top Uploaders</div>
    <div class="content">
      <table width="100%">
        <?php foreach($topuploaders as $p): ?>
          <tr><td><?php echo link_to($p->name, 'player/showNumericSteamId?id='.$p->numeric_steamid) ?></td><td><?php echo $p->num_logs ?></td></tr>
        <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>
  
  <br class="hardSeparator"/>
  
  <div id="recentlyAdded" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Recently Added</div>
    <div class="content">
      <table width="100%">
      <?php foreach($recentlyAdded as $l): ?>
        <tr><td><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></td><td style="white-space: nowrap;"><?php echo getHumanReadableDate($l->getDateTimeObject('created_at')) ?></td></tr>
      <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>
  
  <br class="hardSeparator"/>
  <div id="frontPageCacheInterval">Cached Every 5 Minutes</div>
</div>
