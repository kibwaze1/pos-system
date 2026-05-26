@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="row">
    <!-- Store Information -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Store Information</div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label>Store Name</label><input type="text" name="store_name" class="form-control" value="{{ $settings['store_name'] ?? 'POS System' }}"></div>
                    <div class="mb-3"><label>Address</label><textarea name="store_address" class="form-control">{{ $settings['store_address'] ?? '' }}</textarea></div>
                    <div class="mb-3"><label>Phone</label><input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] ?? '' }}"></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="store_email" class="form-control" value="{{ $settings['store_email'] ?? '' }}"></div>
                    <button type="submit" class="btn btn-primary">Save Store Info</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tax & Currency -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Tax & Currency</div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label>Tax Rate (%)</label><input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ $settings['tax_rate'] ?? 0 }}"></div>
                    <div class="mb-3">
                        <label>Currency Symbol</label>
                        <select name="currency_symbol" class="form-control">
                            @foreach($currencies as $symbol => $display)
                                <option value="{{ $symbol }}" {{ ($settings['currency_symbol'] ?? '$') == $symbol ? 'selected' : '' }}>{{ $display }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Tax & Currency</button>
                </form>
            </div>
        </div>

        <!-- Receipt Customization -->
        <div class="card">
            <div class="card-header">Receipt Customization</div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label>Receipt Header Message</label><textarea name="receipt_header" class="form-control">{{ $settings['receipt_header'] ?? 'Thank you!' }}</textarea></div>
                    <div class="mb-3"><label>Receipt Footer Message</label><textarea name="receipt_footer" class="form-control">{{ $settings['receipt_footer'] ?? 'Visit again!' }}</textarea></div>
                    <button type="submit" class="btn btn-primary">Save Receipt Settings</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Thermal Printer Configuration (new) -->
    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-header">Thermal Printer Configuration</div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Enable Direct Printing</label>
                        <select name="printer_enabled" class="form-control">
                            <option value="0" {{ ($settings['printer_enabled'] ?? '0') == '0' ? 'selected' : '' }}>Disabled</option>
                            <option value="1" {{ ($settings['printer_enabled'] ?? '0') == '1' ? 'selected' : '' }}>Enabled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Connection Type</label>
                        <select name="printer_connection_type" class="form-control" id="printerType">
                            <option value="network" {{ ($settings['printer_connection_type'] ?? '') == 'network' ? 'selected' : '' }}>Network (TCP/IP)</option>
                            <option value="windows" {{ ($settings['printer_connection_type'] ?? '') == 'windows' ? 'selected' : '' }}>Windows Shared Printer</option>
                            <option value="usb" {{ ($settings['printer_connection_type'] ?? '') == 'usb' ? 'selected' : '' }}>USB / Serial (Linux)</option>
                        </select>
                    </div>
                    <div id="networkFields" style="{{ ($settings['printer_connection_type'] ?? '') == 'network' ? 'display:block' : 'display:none' }}">
                        <div class="mb-3"><label>Printer IP Address</label><input type="text" name="printer_network_ip" class="form-control" value="{{ $settings['printer_network_ip'] ?? '192.168.1.100' }}"></div>
                        <div class="mb-3"><label>Port</label><input type="number" name="printer_network_port" class="form-control" value="{{ $settings['printer_network_port'] ?? '9100' }}"></div>
                    </div>
                    <div id="windowsFields" style="{{ ($settings['printer_connection_type'] ?? '') == 'windows' ? 'display:block' : 'display:none' }}">
                        <div class="mb-3"><label>Windows Share Name (e.g., \\\\localhost\\POS-80)</label><input type="text" name="printer_windows_share" class="form-control" value="{{ $settings['printer_windows_share'] ?? '' }}"></div>
                    </div>
                    <div id="usbFields" style="{{ ($settings['printer_connection_type'] ?? '') == 'usb' ? 'display:block' : 'display:none' }}">
                        <div class="mb-3"><label>USB Device Path (Linux: /dev/usb/lp0)</label><input type="text" name="printer_usb_path" class="form-control" value="{{ $settings['printer_usb_path'] ?? '' }}"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Printer Settings</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Database Backup -->
    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-header">Database Backup</div>
            <div class="card-body">
                <form action="{{ route('settings.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Create Database Backup</button>
                </form>
                <p class="text-muted mt-2">Backups are saved in <code>storage/app/backup/</code></p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePrinterFields() {
        let type = document.getElementById('printerType').value;
        document.getElementById('networkFields').style.display = type === 'network' ? 'block' : 'none';
        document.getElementById('windowsFields').style.display = type === 'windows' ? 'block' : 'none';
        document.getElementById('usbFields').style.display = type === 'usb' ? 'block' : 'none';
    }
    let printerType = document.getElementById('printerType');
    if (printerType) {
        printerType.addEventListener('change', togglePrinterFields);
    }
</script>
@endsection
