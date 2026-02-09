{{-- <iframe 
    src="http://reports.dvodeoro.local:8080/jasperserver/flow.html?pp=u%3DJamshasadid%7Cr%3DManager%7Co%3DEMEA,Sales%7Cpa1%3DSweden&_flowId=viewReportFlow&_flowId=viewReportFlow&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2Faccomplishments&reportUnit=%2Freports%2Faccomplishments%2Faccomplishment&standAlone=true&decorate=no&
output=pdf" 
    frameborder="0"
    width="100%" 
    height="800px" >

</iframe> --}}

@php
    // Jasper credentials and report parameters
    $jasperUser = 'Jamshasadid';
    $jasperRole = 'Manager';
    $jasperOrg = 'EMEA,Sales';
    $jasperPa1 = 'Sweden';

    // Build the parameter string for Jasper
    $pp = "u={$jasperUser}|r={$jasperRole}|o={$jasperOrg}|pa1={$jasperPa1}|department_id={$department_id}|year={$year}|month={$month}";

    // Build the iframe URL for HTML output (displayable in iframe)
    $iframeUrl = "http://reports.dvodeoro.local:8080/jasperserver/flow.html?" .
                 "pp=" . urlencode($pp) .
                 "&_flowId=viewReportFlow" .
                 "&ParentFolderUri=%2Freports%2Faccomplishments" .
                 "&reportUnit=%2Freports%2Faccomplishments%2Faccomplishment" .
                 "&standAlone=true&decorate=no&output=html";
@endphp

<div style="height: 800px;">
    <iframe 
        src="{{ $iframeUrl }}" 
        frameborder="0" 
        width="100%" 
        height="100%">
    </iframe>
</div>
