<?php

?>

<h1>
    <strong>Hello World!</strong>
    <br><br><br><br>
    <strong>Registration</strong>
</h1>

<?php
if (!empty($success)) {
    echo '<div style="color: green;">' . htmlspecialchars($success) . '</div>';
}
if (!empty($error)) {
    echo '<div style="color: red;">' . htmlspecialchars($error) . '</div>';
}
?>

<form method="post" action="">
    <div>
        <label for="meno">Meno:</label>
        <input type="text" id="meno" name="meno" required>
    </div>
    <div>
        <label for="priezvisko">Priezvisko:</label>
        <input type="text" id="priezvisko" name="priezvisko" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="pohlavie">Pohlavie:</label>
        <select id="pohlavie" name="pohlavie" required>
            <option value="M">Muž</option>
            <option value="Ž">Žena</option>
        </select>
    </div>
    <div>
        <button type="submit">Registrovať</button>
    </div>
</form>
