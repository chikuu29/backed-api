<!DOCTYPE html>
<html>
<head>
    <title>PDF Demo</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        .container {
            width: 170mm;
            height: 260mm;
            margin: 0 auto;
            border: 2px solid green;
            border-style: dashed;
            padding: 20px;
            box-sizing: border-box;
        }
        .imgdiv {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header img, .profile-photo img, .imgclass {
            height: 150px;
            width: 250px;
        }
        .content {
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            margin-top: 30px;
        }
        .content div {
            font-size: 30px;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        .content span {
            color: green;
        }
        .content span.red {
            color: red;
        }
        .footer {
            text-align: center;
            padding: 5px;
            height: auto;
            width: 300px;
            background-color: yellow;
            margin: 20px 0 0 0;
            position: relative;
            left: 150px;
            border-radius: 10px;
        }
        .footer span {
            color: green;
        }
        .footer span.red {
            color: red;
        }
    </style>
</head>
<body>
    @foreach ($dynamicData as $data)
    <div class="container">
        <div class="imgdiv">
            <div class="header">
                <img src="{{ $data['img'] }}" alt="Logo" class="imgclass">
            </div>
            <div class="profile-photo">
                <img src="{{ $data['profile'] }}" alt="Profile Photo" class="imgclass">
            </div>
            <div>
                <h3 style="color: white; background-color: green; float: right; top: -40px; position: relative; border-radius: 10px; padding: 5px;">
                    <span>USER ID:</span><span>{{ $data['id'] }}</span>
                </h3>
            </div>
        </div>
        <div class="content">
            <div>
                <span>DOB:-</span><span class="red">{{ $data['dob'] }}</span>
            </div>
            <div>
                <span>Height:-</span><span class="red">{{ $data['hieght'] }}</span>
            </div>
            <img src="{{ $data['lodganesh'] }}" alt="ganesh" style="height: 200px; width: 200px; position: absolute; right: 10px; top: 300px">
            <div>
                <span>Colour:-</span><span class="red">{{ $data['color'] }}</span>
            </div>
            <div>
                <span>Rashi:-</span><span class="red">{{ $data['rasi'] }}</span>
            </div>
            <div>
                <span>Qualification:-</span><span class="red">{{ $data['userhighesteducation'] }}</span>
            </div>
            <div>
                <span>Occupation:-</span><span class="red">{{ $data['occupation'] }}</span>
            </div>
            <div>
                <span>Designation:-</span><span class="red">{{ $data['deg'] }}</span>
            </div>
            <div>
                <span>Annual Income:-</span><span class="red">{{ $data['income'] }}</span>
            </div>
            <div>
                <span>Job Location:-</span><span class="red">{{ $data['joblocation'] }}</span>
            </div>
            <div>
                <span>Hometown:-</span><span class="red">{{ $data['hometown'] }}</span>
            </div>
            <div>
                <span>Contact:-</span><span class="red">{{ $data['phone'] }}</span>
            </div>
            <div>
                <span>WhatsApp:-</span><span class="red">{{ $data['whats'] }}</span>
            </div>
        </div>
        <div class="footer">
            <div>
                <span>Website:-</span>
                <span>
                    <a href="https://choicemarriage.com">www.choicemarriage.com</a>
                </span>
            </div>
            <div>
                <span>Contact:-</span>
                <span class="red">{{ $data['phoneadmin'] }}</span>
            </div>
        </div>
    </div>

    @if (!$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>
