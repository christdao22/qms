function certAppearance(data, setting) {
    var today   = new Date(data.created_at);
    var month   = today.toLocaleString('default', { month: 'long' });
    var day     = today.getDate();
    var year    = today.getFullYear();
    var date    = month + ' ' + day + ", " + year;
    var time    = today.toLocaleString("en-PH", {
                    hour: "numeric",
                    minute: "numeric",
                    hour12: true,
                });

    var base_url    = window.location.origin + '/qms/';
    var bookMan     = base_url + '/assets/fonts/BOOKOS.TTF';
    var oldFont     = base_url + '/assets/fonts/OLD.ttf';

    var institution = data.school_name == null && data.other_institution != ''?data.other_institution:data.school_name;

    var content = `
        <style type='text/css'>
            @media print {
                @page { size: A4; margin: 0.2in; }
                @font-face { font-family: old; src: url(${oldFont}); }
                @font-face { font-family: bookMan; src: url(${bookMan}); }
                html, body { box-sizing: border-box; display:block; width: 100vw; height: 100vh; margin: 0 auto !important; padding: 0 !important; list-style-type: none !important; }
                img { width: 100%; }
                hr { border: 2px dashed black; }
                .receipt-token { width: 100vw; min-height: 50%; margin:auto; text-align: center; display: inline-flex; justify-content: center; align-items: center; }
                .receipt-token h1 { margin: 0; padding: 0; font-size: 8vw; line-height: 10vw; text-align: center; font-weight: bold; }
                .receipt-token h4 { margin: 0; padding: 0; font-size: 4vw; line-height: 7vw; text-align: center }
                .receipt-token ul { margin: 0; padding: 0; font-size: 4vw; line-height: 7vw; text-align: center; list-style: none; }
                .col1 { padding: 15px; border: 2px solid black; text-align: center; width: 80%; height: 50%; padding-left: 30px; }
                .col2 { width: 100%; display: flex; align-items: center; justify-content: center; }
                .logo { width: 60px; display: block; margin: 0 auto 10px; }
                .cert { position: relative; font-family: bookMan; display: block; margin: auto; text-align: center; width: 100%; height: 50%; font-size: 16px; margin-top: 15px; padding-top: 15px; border: 2px solid black; }
                .header p { line-height: 18px; }
                .old { font-family: old; font-size: 16px; }
                .ca-content { text-align: center; width: 90%; margin: 20px auto 0; }
                .ca-content h4 { margin-bottom:30px; font-size: 45px; }
                .allcaps { text-transform:uppercase; }
            }
        </style>`;

    content += `
        <div class="receipt-token">
            <div class='col1'>
                <h4>Queuing Number</h4>
                <h1>${data.token_no}</h1>
                <ul class='list-unstyled'>
                    <li><p><strong>Department </strong>${data.department}</p></li>
                    <li><p><strong>Officer </strong>${data.firstname} ${data.lastname}</p></li>
                </ul>
            </div>
            <div class='col2'>
                <img src='${base_url+setting.survey_qr}' alt='qr'>
            </div>
        </div>
        <hr>
        <div class='cert'>
            <div class='header'>
                <img class='logo' src='${base_url+setting.deped_seal}' alt='Deped Seal'>
                <div class=''>
                    <p>
                        <strong class='old'>Republic of the Philippines</strong> <br>
                        <strong class='old' style='font-size: 18px;'>Department of Education</strong> <br>
                        ${setting.address}, <br>${setting.city}, Philippines
                    </p>
                </div>
            </div>
            <div class='ca-content'>
                <h4 class='old'>Certificate of Appearance</h4>
                <p>This is to certify that Mr./Ms. <strong class="allcaps">${data.client_name}</strong> `+ ' ' +` of
                <strong class='allcaps'>${institution}</strong>`+ ' ' +` appeared at ${setting.office},
                ${setting.address}, `+ ' ' +` ${setting.city} on ${date}.</p>
                <p class='' style="margin-top:50px; line-height:18px;">
                    <strong class="allcaps">${setting.sds_fullname}</strong> <br>
                    <span>Administrative Officer V</span> <br>
                </p>
                <p style="margin: auto; left: 0; right: 0; font-size:12px; margin-top: 35px; position: absolute; bottom: 15px;">${date} ${time}</p>
            </div>
        </div>`;

    return content;
}

function openPDFInNewTab(token, setting) {
    var content = certAppearance(token, setting);
    var newTab = window.open();
    newTab.document.write(content);
    newTab.document.close();
}
