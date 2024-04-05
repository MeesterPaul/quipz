<?php
// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

$icsRemote = "https://sync.infomaniak.com/calendars/PR02438/5d2e2793-05f1-4f32-b4be-06f27b899168?export";
$icsLocal = "quipz-events.ics";

$refreshCache = false;
$timezone = new DateTimeZone('Europe/Zurich');
$now = new DateTime('now', $timezone );
$nowUTC = new DateTime('now', new DateTimeZone('UTC'));

require_once '../vendor/autoload.php';

use ICal\ICal;

if(file_exists($icsLocal)){
    // check age
    $lastRefreshed = filemtime($icsLocal);
    if(!$lastRefreshed || ($nowUTC->format('U') - $lastRefreshed) > 3600 ){
        $refreshCache = true;
    }
} else {
    $refreshCache = true;
}

if($refreshCache){
    file_put_contents($icsLocal, file_get_contents($icsRemote));
}


try {
    $ical = new ICal($icsLocal, array(
        'defaultSpan'                 => 2,     // Default value
        'defaultTimeZone'             => 'UTC',
        'defaultWeekStart'            => 'MO',  // Default value
        'disableCharacterReplacement' => false, // Default value
        'filterDaysAfter'             => null,  // Default value
        'filterDaysBefore'            => null,  // Default value
        'httpUserAgent'               => null,  // Default value
        'skipRecurrence'              => false, // Default value
    ));
    // $ical->initFile('ICal.ics');
    // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics', $username = null, $password = null, $userAgent = null);
} catch (\Exception $e) {
    die($e);
}
?>
<?php /*
    <h4 class="mt-3 mb-2">PHP ICS Parser example</h3>
    <ul class="list-group">
        <li class="list-group-item">
            The number of events
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->eventCount ?></span>
        </li>
        <li class="list-group-item">
            The number of free/busy time slots
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->freeBusyCount ?></span>
        </li>
        <li class="list-group-item">
            The number of todos
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->todoCount ?></span>
        </li>
        <li class="list-group-item">
            The number of alarms
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->alarmCount ?></span>
        </li>
    </ul>
*/ ?>
    <?php
        $showExample = array(
            'interval' => false,
            'range'    => true,
            'all'      => false,
        );
    ?>

    <?php /*
        if ($showExample['interval']) {
            $events = $ical->eventsFromInterval('1 week');

            if ($events) {
                echo '<h4 class="mt-3 mb-2">Events in the next 7 days:</h4>';
            }

            $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mt-3 mb-2"><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } */ ?>

    <?php
        if ($showExample['range']) {
            $events = $ical->eventsFromRange($now->format('Y-m-d 00:00:00'), '2999-12-31 23:59:59');

            if ($events) {
                foreach ($events as $event) { ?>
                    <article>
                        <h2><?php
                            $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                            echo $event->summary;
                        ?></h2>
                        <h3 class="event-date"><?php echo $dtstart->format('l, F j Y \a\t H:i \C\E\T') ?></h3>
                        <div class="event-location" style="font-style: italic;"><a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($event->location); ?>" target="_blank"><?php echo $event->location; ?></a></div>
                        <div class="event-description">
                            <?php 
                            $description = preg_replace('/((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/', '<a href="\1">\1</a>', $event->description);
                            echo nl2p($description); ?>
                        </div>
                    </article>
                <?php
                }
            }
    ?>
    <?php } ?>

    <?php /*
        if ($showExample['all']) {
            $events = $ical->sortEventsWithOrder($ical->events());

            if ($events) {
                echo '<h4 class="mt-3 mb-2">All Events:</h4>';
            }
    ?>
    <div class="row">
    <?php
    $count = 1;
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mt-3 mb-2"><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } */ ?>
<?php
function nl2p($string, $line_breaks = true, $xml = true) {

$string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

// It is conceivable that people might still want single line-breaks
// without breaking into a new paragraph.
if ($line_breaks == true)
    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
else 
    return '<p>'.preg_replace(
    array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
    array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),

    trim($string)).'</p>'; 
}