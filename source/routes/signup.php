<h3>Sign up!</h3>
<p>Ser ud til du ikke er i systemet (endnu).</p>

<h1> OBS: denne side er midlertidig og fungere egentligt ikke</h1>

<p>For at indskrive dig på et hold, skal du først registrer dig.</p>
<form action="/register" method="POST">
    <div class="form-group">
        <label for="in-auid">AU-ID</label>
        <input type="text" class="form-control" id="in-auid" placeholder="<?= $_SESSION["auid"] ?>" disabled>
    </div>
    <div class="form-group">
        <label for="in-card">Årskort</label>
        <input type="number" class="form-control" id="in-card" placeholder="20XX XX XXX" style="-webkit-appearance: none;">
    </div>
    <div class="form-group">
        <label for="in-name">Navn</label>
        <input type="text" class="form-control" id="in-name" placeholder="Bjarne Stroustrup" required="true">
    </div>
    <div class="form-group">
        <label for="in-email">E-mailadresse</label>
        <input type="email" class="form-control" id="in-email" placeholder="[årskort]@post.au.dk eller en mail du tjekker" required="false">
    </div>
    <div class="form-group">
        <label for="in-phone">Telefon</label>
        <div class="input-group">
            <span class="input-group-addon">+45</span>
            <input type="tel" class="form-control" id="in-phone" placeholder="00 00 00 00" aria-describedby="basic-addon1">
        </div>
    </div>
    <button type="submit" class="btn btn-default">Sign up!</button>
</form>
<?php
// HTML
// Du kan indskrive dig på et hold her: <br>
// <form  action="/apply" class="form-inline" method="POST">
//     <div class="form-group">
//         <select class="form-control" name="team">
// $sql = "SELECT A.id, A.name, A.slug FROM team A
//         INNER JOIN teamstudent B ...";
// $result = $db->query($sql);
// while ($row = $result->fetch_assoc()) {
//     echo "<option value=\"$row[id]\">$row[name] ($row[slug])</option>";
// }
// $result->free();
// HTML
//         </select>
//     </div>
//     <div class="form-group">
//         <input type="submit" class="btn btn-default" value="Send ansøgning"></input>
//     </div>
// </form>
?>