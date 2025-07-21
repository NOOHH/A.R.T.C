<div style="width: 900px; height: 650px; border: 4px solid #222; padding: 0; background: #fff; font-family: Arial, sans-serif; position: relative;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 40px 0 40px;">
        <img src="{{ asset('resources/images/ARTC_Logo.png') }}" alt="Logo" style="height: 90px;">
        <div style="text-align: right;">
            <img src="{{ asset('resources/images/sample1.png') }}" alt="Writing" style="height: 70px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 0 8px #ccc;">
        </div>
    </div>
    <div style="text-align: center; margin-top: -40px;">
        <span style="font-size: 2.5rem; font-weight: bold; color: #b71c1c; letter-spacing: 2px;">ASCENDO</span>
        <span style="font-size: 2.1rem; font-weight: bold; color: #444; margin-left: 10px;">REVIEW AND TRAINING CENTER</span>
    </div>
    <div style="margin: 30px 0 0 0; text-align: center;">
        <span style="font-size: 2.2rem; font-weight: bold; letter-spacing: 2px;">CERTIFICATE OF <span style='color: #e53935;'>ENROLLMENT</span></span>
    </div>
    <div style="margin: 20px 0 0 0; text-align: center; font-size: 1.3rem; color: #444;">This is to certify that</div>
    <div style="margin: 10px 0 0 0; text-align: center; font-size: 2.1rem; font-weight: bold; color: #222;">
        {{ $student_name }}
    </div>
    <div style="margin: 20px 0 0 0; text-align: center; font-size: 1.1rem; color: #222; line-height: 1.5;">
        has successfully ENROLLED in ASCENDO Review and Training Center <span style="font-style: italic;">{{ $batch }}</span><br>
        Program Details:<br>
        <span style="font-weight: bold; margin: 10px 0; display: block;">{{ $program_details }}</span>
        
        @if($plan_type === 'modular' && !empty($module_names))
            <div style="margin-top: 15px;">
                <span style="font-weight: bold; color: #e53935;">ENROLLED MODULES/COURSES:</span><br>
                <div style="font-size: 1.0rem; margin-top: 8px; text-align: left; max-width: 600px; margin-left: auto; margin-right: auto;">
                    @foreach($module_names as $moduleName)
                        @if(strpos($moduleName, '  • Courses:') === 0)
                            <div style="margin-left: 20px; font-style: italic; color: #666;">{{ $moduleName }}</div>
                        @else
                            <div style="margin: 5px 0; font-weight: bold;">• {{ strtoupper($moduleName) }}</div>
                        @endif
                    @endforeach
                </div>
            </div>
        @elseif($plan_type === 'modular')
            <span style="font-weight: bold; color: #e53935;">MODULAR ENROLLMENT</span>
        @else
            <span style="font-weight: bold; color: #e53935;">FULL PROGRAM ENROLLMENT</span>
        @endif
    </div>
    <div style="position: absolute; left: 40px; bottom: 80px; font-size: 0.9rem; color: #444;">
        Date of Completion: <span style="font-weight: bold;">{{ $completion_date }}</span>
    </div>
    <div style="position: absolute; left: 40px; bottom: 40px; font-size: 0.8rem; color: #444;">
        Note:<br>
        - Digital signature and verification system are embedded in the QR code
    </div>
    <div style="position: absolute; right: 40px; bottom: 40px;">
        <img src="{{ $qr_code_src }}" alt="QR Code" style="height: 90px;">
    </div>
</div>
    </div>
    <div style="position: absolute; right: 40px; bottom: 40px;">
        <img src="{{ $qr_code_src }}" alt="QR Code" style="height: 90px;">
    </div>
</div> 