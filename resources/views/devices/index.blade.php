"use client";

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Data Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Device Data Viewer</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <form id="deviceForm" class="row g-3">
                    <div class="col-md-6">
                        <label for="imei" class="form-label">IMEI Number</label>
                        <input type="text" class="form-control" id="imei" name="imei" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Get Device Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="deviceTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            let table = $('#deviceTable').DataTable({
                searching: false,
                paging: false,
                info: false
            });

            // Define the fields you want to display
            const displayFields = {
                'imei':             'IMEI',
                'iccid':            'ICCID',
                'version':          'Version',
                'server':           'Server',
                'getIp':            'IP Address',
                'csq':              'Signal Strength',
                'bat':              'Battery',
                'voltage':          'Voltage',
                'firstTime':        'First Connection',
                'lastTime':         'Last Connection',
                'offLineDays':      'Offline Days',
                'todayLogin':       'Today Logins',
                'apn':              'APN',
                'input1':           'INPUT1',
                'input2':           'INPUT2',
                'relay1':           'RELAY1',
                'relay2':           'RELAY2',
                'eventset_avd':     'EVENTSET, AVD',
                'eventset_aepld':   'EVENTSET, AEPLD',
                'video':            'VIDEO',
                'acc':              'ACC',
                'gps':              'GPS',
                'signalLevel':      'Signal Level',
                'ip_addr':          'IP_ADDR',
                'checkvideo':       'CHECKVIDEO',
                'statusvideo':      'STATUSVIDEO',
                'timeZone':         'Time Zone',
                'time':             'Time',
                'onVideo':          'On video',
                'cameraInsertion':  'Camera insertion',
                'tf':               'TF',
                'memory':           'Memory'
            };

            $('#deviceForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '/device-data',
                    method: 'POST',
                    data: {
                        imei: $('#imei').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.clear();
                        
                        // Only add the fields we want to display
                        for (let key in displayFields) {
                            if (response[key] !== undefined) {
                                table.row.add([
                                    displayFields[key],
                                    response[key]
                                ]);
                            }
                        }
                        
                        table.draw();
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.error || 'Failed to fetch device data'));
                    }
                });
            });
        });
    </script>
</body>
</html> 