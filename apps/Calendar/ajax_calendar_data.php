<?php

// Set content type to JSON
header('Content-Type: application/json');

// Initialize scheduled items array
$scheduledItems = [];

// Error reporting (optional, for development)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// 1. Include Configuration
$config_paths = [
    __DIR__ . '/config.php',   // If script is in the web root
];

$config_found = false;
foreach ($config_paths as $config_path) {
    if (file_exists($config_path)) {
        include_once $config_path;
        $config_found = true;
        break;
    }
}

if (!$config_found) {
    echo json_encode(['error' => 'Configuration file not found.']);
    exit;
}

if (!isset($con) || !$con) {
    echo json_encode(['error' => 'Database connection not established. Check config.php. Error: ' . mysqli_connect_error()]);
    exit;
}

// 2. Determine Date Range
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');

// Validate year and month
if ($year < 1970 || $year > 2100 || $month < 1 || $month > 12) {
    echo json_encode(['error' => 'Invalid year or month provided.']);
    exit;
}

$displayStartDate = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
// Calculate the first day of the month *after* the next month, then subtract one day to get the last day of the next month.
// This covers a two-month view.
$displayEndDate = date('Y-m-d', mktime(0, 0, 0, $month + 2, 0, $year));


// 3. Fetch Data from Database

// Helper function to execute prepared statements
function execute_query($connection, $sql, $params, $types) {
    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) {
        return ['error' => 'Prepare failed: (' . mysqli_errno($connection) . ') ' . mysqli_error($connection)];
    }

    if (!empty($params) && !empty($types)) {
        if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
            return ['error' => 'Bind param failed: (' . mysqli_stmt_errno($stmt) . ') ' . mysqli_stmt_error($stmt)];
        }
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        return ['error' => 'Execute failed: (' . mysqli_stmt_errno($stmt) . ') ' . mysqli_stmt_error($stmt)];
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        // For SELECT queries, get_result can fail if there are no results, but also on actual errors.
        // mysqli_stmt_error($stmt) might give more info here.
        if (mysqli_stmt_errno($stmt)) {
             return ['error' => 'Get result failed: (' . mysqli_stmt_errno($stmt) . ') ' . mysqli_stmt_error($stmt)];
        }
        // No results is not an error for our case, so return an empty array
        return []; 
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $data;
}

// Initialize $scheduledItems for the date range
$currentDate = new DateTime($displayStartDate);
$endDateObj = new DateTime($displayEndDate);
while ($currentDate <= $endDateObj) {
    $dateStr = $currentDate->format('Y-m-d');
    $scheduledItems[$dateStr] = [
        'calendarEvents' => [],
        'shipmentsArrived' => [],
        'shipmentsETA' => [],
        'shipmentsSent' => [],
        'phoneNotes' => []
    ];
    $currentDate->modify('+1 day');
}


// Fetch Calendar Events
$sqlCalendarEvents = "
    SELECT * FROM calendarevents 
    WHERE 
        ( (endday IS NULL OR endday = '' OR endday = '0') AND STR_TO_DATE(CONCAT(year, '-', month, '-', day), '%Y-%m-%d') BETWEEN ? AND ? ) OR 
        ( (endday IS NOT NULL AND endday != '' AND endday != '0') AND 
          STR_TO_DATE(CONCAT(year, '-', month, '-', day), '%Y-%m-%d') <= ? AND 
          STR_TO_DATE(CONCAT(endyear, '-', endmonth, '-', endday), '%Y-%m-%d') >= ? );
";
$calendarEventsResult = execute_query($con, $sqlCalendarEvents, [$displayStartDate, $displayEndDate, $displayEndDate, $displayStartDate], "ssss");

if (isset($calendarEventsResult['error'])) {
    echo json_encode(['error' => 'Error fetching calendar events: ' . $calendarEventsResult['error']]);
    exit;
}

foreach ($calendarEventsResult as $event) {
    $eventStartDateStr = sprintf('%04d-%02d-%02d', $event['year'], $event['month'], $event['day']);
    
    if (!empty($event['endyear']) && !empty($event['endmonth']) && !empty($event['endday']) && $event['endday'] != '0') {
        $eventEndDateStr = sprintf('%04d-%02d-%02d', $event['endyear'], $event['endmonth'], $event['endday']);
        $currentEventDate = new DateTime($eventStartDateStr);
        $eventEndDate = new DateTime($eventEndDateStr);

        while ($currentEventDate <= $eventEndDate) {
            $dateKey = $currentEventDate->format('Y-m-d');
            if (isset($scheduledItems[$dateKey])) {
                $scheduledItems[$dateKey]['calendarEvents'][] = $event;
            }
            $currentEventDate->modify('+1 day');
        }
    } else {
        if (isset($scheduledItems[$eventStartDateStr])) {
            $scheduledItems[$eventStartDateStr]['calendarEvents'][] = $event;
        }
    }
}

// Fetch Shipments
$sqlShipments = "
    SELECT id, datesent, datereceived, title, city, eta FROM shipments 
    WHERE (datereceived >= ? AND datereceived <= ?) OR 
          (eta >= ? AND eta <= ?) OR 
          (datesent >= ? AND datesent <= ?);
";
// Ensure dates are valid or NULL before using in query
$shipmentsResult = execute_query($con, $sqlShipments, [
    $displayStartDate, $displayEndDate, 
    $displayStartDate, $displayEndDate, 
    $displayStartDate, $displayEndDate
], "ssssss");

if (isset($shipmentsResult['error'])) {
    echo json_encode(['error' => 'Error fetching shipments: ' . $shipmentsResult['error']]);
    exit;
}

foreach ($shipmentsResult as $shipment) {
    // Shipments Arrived
    if (!empty($shipment['datereceived']) && $shipment['datereceived'] != '0000-00-00') {
        $dateKey = date('Y-m-d', strtotime($shipment['datereceived']));
         if (isset($scheduledItems[$dateKey])) {
            $scheduledItems[$dateKey]['shipmentsArrived'][] = $shipment;
        }
    }
    // Shipments ETA
    if (!empty($shipment['eta']) && $shipment['eta'] != '0000-00-00') {
        $dateKey = date('Y-m-d', strtotime($shipment['eta']));
         if (isset($scheduledItems[$dateKey])) {
            $scheduledItems[$dateKey]['shipmentsETA'][] = $shipment;
        }
    }
    // Shipments Sent
    if (!empty($shipment['datesent']) && $shipment['datesent'] != '0000-00-00') {
        $dateKey = date('Y-m-d', strtotime($shipment['datesent']));
        if (isset($scheduledItems[$dateKey])) {
            $scheduledItems[$dateKey]['shipmentsSent'][] = $shipment;
        }
    }
}


// Fetch Phone Notes
$sqlPhoneNotes = "
    SELECT id, date, firstname, lastname FROM phonenotes 
    WHERE date BETWEEN ? AND ?;
";
$phoneNotesResult = execute_query($con, $sqlPhoneNotes, [$displayStartDate, $displayEndDate], "ss");

if (isset($phoneNotesResult['error'])) {
    echo json_encode(['error' => 'Error fetching phone notes: ' . $phoneNotesResult['error']]);
    exit;
}

foreach ($phoneNotesResult as $note) {
    if (!empty($note['date']) && $note['date'] != '0000-00-00') {
        $dateKey = date('Y-m-d', strtotime($note['date']));
         if (isset($scheduledItems[$dateKey])) {
            $scheduledItems[$dateKey]['phoneNotes'][] = $note;
        }
    }
}

// 5. Return JSON Response
echo json_encode($scheduledItems);

// Close connection if it was opened here (though typically managed by config.php)
if (isset($con) && is_object($con) && method_exists($con, 'close')) {
   // mysqli_close($con); // Commented out as config.php might handle this, or it might be persistent.
}

exit;
?>
