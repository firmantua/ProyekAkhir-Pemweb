<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan Kamar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .header svg {
            width: 32px;
            height: 32px;
        }

        .room-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            justify-content: center;
            margin: 20px;
        }

        .room {
            width: 60px;
            height: 60px;
            border: 2px solid black;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
        }

        .room.occupied {
            background-color: red;
            color: white;
            cursor: not-allowed;
        }

        .room.available {
            background-color: lightgray;
            color: black;
        }

        .room.selected {
            background-color: green;
            color: white;
        }

        .legend {
            margin-top: 20px;
        }

        .legend span {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border: 1px solid black;
        }

        .legend .occupied {
            background-color: red;
        }

        .legend .available {
            background-color: lightgray;
        }

        .legend .selected {
            background-color: green;
        }

        .buttons {
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: black;
            color: white;
        }

        button:hover {
            background-color: gray;
        }
    </style>
    <script>
        let selectedRoom = null;

        function toggleRoomSelection(room) {
            if (room.classList.contains('occupied')) {
                return;
            }

            if (selectedRoom) {
                selectedRoom.classList.remove('selected');
            }

            if (selectedRoom !== room) {
                room.classList.add('selected');
                selectedRoom = room;
            } else {
                selectedRoom = null;
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
        </svg>
        <h1>Kita Dorm</h1>
    </div>

    <h2>Pemilihan Kamar</h2>

    <div class="room-container">
        <div class="room occupied">K101</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K102</div>
        <div class="room occupied">K103</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K104</div>
        <div class="room occupied">K105</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K106</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K107</div>
        <div class="room occupied">K108</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K109</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K110</div>
        <div class="room occupied">K111</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K112</div>
        <div class="room occupied">K113</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K114</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K115</div>
        <div class="room occupied">K116</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K117</div>
        <div class="room occupied">K118</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K119</div>
        <div class="room available" onclick="toggleRoomSelection(this)">K120</div>
    </div>

    <div class="legend">
        <span class="occupied"></span> Penuh
        <span class="available"></span> Tersedia
        <span class="selected"></span> Terpilih
    </div>

    <div class="buttons">
        <button onclick="alert('Melanjutkan ke langkah berikutnya!')">Lanjut</button>
    </div>
</body>
</html>
