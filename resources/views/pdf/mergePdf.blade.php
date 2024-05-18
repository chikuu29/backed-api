<!DOCTYPE html>
<html>
<head>
    <title>PDF Demo</title>
</head>
<body>
    @foreach ($dynamicData as $data)
    <div class="container" style="width: 170mm; height: 260mm; margin: 0 auto; border: 2px solid green; border-style: dashed; padding: 20px; box-sizing: border-box;">
        <div class="imgdiv" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="header" style="float: right;">
                <img src="{{ $data['img'] }}" alt="Logo" class="imgclass" style="height: 150px; width: 250px;">
            </div>
            <div class="profile-photo">
                <img src="{{ $data['profile'] }}" alt="Profile Photo" class="imgclass" style="height: 200px; width: 200px;">
            </div>
            <div>
                <h3 style="color: white; background-color: green; float: right; top: -40px; position: relative; border-radius: 10px; padding: 5px;">
                    <span>USER ID:</span><span>{{ $data['id'] }}</span>
                </h3>
            </div>
        </div>
        <div class="content" style="font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; margin-top: 30px;">
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">DOB:-</span><span style="color: red;"> {{ $data['dob'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Height:-</span><span style="color: red;"> {{ $data['hieght'] }}</span>
            </div>
            <img src="{{ $data['lodganesh'] }}" alt="ganesh" style="height: 200px; width: 200px; position: absolute; right: 10px; top: 300px;">
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Colour:-</span><span style="color: red;"> {{ $data['color'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Rashi:-</span><span style="color: red;"> {{ $data['rasi'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Qualification:-</span><span style="color: red;"> {{ $data['userhighesteducation'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Occupation:-</span><span style="color: red;"> {{ $data['occupation'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Designation:-</span><span style="color: red;"> {{ $data['deg'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Annual Income:-</span><span style="color: red;"> {{ $data['income'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Job Location:-</span><span style="color: red;"> {{ $data['joblocation'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Hometown:-</span><span style="color: red;"> {{ $data['hometown'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">Contact:-</span><span style="color: red;"> {{ $data['phone'] }}</span>
            </div>
            <div style="font-size: 30px; text-decoration: underline; margin-bottom: 20px;">
                <span style="color: green;">WhatsApp:-</span><span style="color: red;"> {{ $data['whats'] }}</span>
            </div>
        </div>
        <div style="text-align: center; padding: 5px; height: auto; width: 300px; background-color: yellow; margin: 20px 0 0 0; position: relative; left: 150px; border-radius: 10px;">
            <div>
                <span style="color: green;">Website:-</span>
                <span>
                    <a href="https://choicemarriage.com">www.choicemarriage.com</a>
                </span>
            </div>
            <div>
                <span style="color: green;">Contact:-</span>
                <span style="color: red;">{{ $data['phoneadmin'] }}</span>
            </div>
        </div>
    </div>

    @if (!$loop->last)
    <div style="page-break-after: always;"></div>
    @endif
    @endforeach
</body>
</html>
