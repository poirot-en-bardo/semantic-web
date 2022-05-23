<!DOCTYPE html>
<html>
<head>
    <title>Da</title>
    <style>

        .sendButton {
            margin-top: 15px;
            margin-left: 60px;
            margin-bottom: 15px;
        }

        input {
            margin: 10px;
        }
    </style>
</head>
<body>


<img src="autobar.jpeg" height="430" width="800">

<h2>Introduceti datele:</h2>

<form id="formular">
    Camp1 <input type="text" name="c1"><br/>
    Camp2 <input type="text" name="c2"><br/>
    Camp3 <input type="text" name="c3"><br/>
</form>

<div>
    <button class="sendButton" onclick="trimite1()">Trimite catre Serverul X</button>
</div>

<div>
    <h2>Date returnate:</h2>
    <table>
        <tr>
            <th>Camp1</th>
            <th>Camp2</th>
            <th>Camp3</th>
            <th>Camp4</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>
<div>
    <button class="sendButton" onclick="trimite2()">Trimite catre Serverul Y</button>
</div>

<div>
    <table>
        <tr>
            <th>Camp1</th>
            <th>Camp2</th>
            <th>Camp3</th>
            <th>Camp4</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

<div>
    <button class="sendButton" onclick="trimite3()">Trimite catre Serverul Z</button>
</div>

<div>
    <table>
        <tr>
            <th>Camp1</th>
            <th>Camp2</th>
            <th>Camp3</th>
            <th>Camp4</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

</body>
</html>

