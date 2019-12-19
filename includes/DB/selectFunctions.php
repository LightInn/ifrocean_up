<?php

require_once "DB_infos.php";

$pdo = new PDO(DB_INFOS::servername, DB_INFOS::username, DB_INFOS::password, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);


function userLogin($email, $password)
{
    global $pdo;
    $query = $pdo->prepare("SELECT * FROM `personnes` WHERE email=:email AND password=:password");
    $query->execute(['email' => $email, 'password' => $password]);
    $row = $query->fetchAll();

    if (empty($row)) {
        msg("Wrong email or password");
    }
    foreach ($row as $r) {
        if ($r["email"] == $email AND $r["password"] == $password) {
            /*Do everything as connected*/
            session_start();
            $personne = [];
            $personne["auth"] = true;
            $personne["id_personnes"] = $r["id_personnes"];
            $personne["nom"] = $r["nom"];
            $personne["prenom"] = $r["prenom"];
            $personne["email"] = $r["email"];
            $personne["password"] = $r["password"];
            if ($r["admin"] == 1) {
                $personne["admin"] = true;
            } else {
                $personne["admin"] = false;
            }
            $_SESSION["personne"] = $personne;

            header('Location: home.php');

        }
    }

}


function userInscription($nom, $prenom, $email, $tel, $password)
{

    global $pdo;
    $query = $pdo->prepare("INSERT INTO `personnes` (`id_personnes`, `nom`, `prenom`, `email`, `tel`, `password`, `admin`) VALUES (NULL, :nom, :prenom, :email, :tel, :password, '0')");
    $query->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'tel' => $tel, 'password' => $password]);
}


function getEtudeListe()
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `etudes` ");
    $rq->execute();
    $data = $rq->fetchAll();
    return $data;
}


function EtudeAdd($nom, $reference)
{
    $date = date('Y/m/d', time());
    global $pdo;
    $query = $pdo->prepare("INSERT INTO `etudes` (`nom`, `dateDebut`,  `reference`) VALUES ( :nom, :date , :referen)");
    $query->execute(['nom' => $nom, 'date' => $date, 'referen' => $reference]);
}


function getEtude($id)
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `etudes` where id_etudes=:id ");
    $rq->execute(['id' => $id]);
    $data = $rq->fetch();
    return $data;
}


function clotureEtude($id)
{

    $date = date('Y/m/d', time());
    global $pdo;
    $query = $pdo->prepare("    UPDATE `etudes` SET `dateFin` = :date WHERE `etudes`.`id_etudes` = :id ");
    $query->execute(['date' => $date, 'id' => $id]);

}


function supprimeEtude($id)
{
    global $pdo;
    $query = $pdo->prepare("DELETE FROM `etudes` WHERE `etudes`.`id_etudes` = :id");
    $query->execute(['id' => $id]);
}


function getPlageInstance($id)
{
    global $pdo;
    $rq = $pdo->prepare("SELECT p.nom, p.commune,p.departement,i.id_instancePlages FROM instanceplages i JOIN etudes e on i.FK_id_etudes = e.id_etudes JOIN plage p on i.FK_id_plages = p.id_plages WHERE e.id_etudes=:id");
    $rq->execute(['id' => $id]);
    $data = $rq->fetchAll();
    return $data;
}


function getPlagesNotInEtude($id)
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM plage");
    $rq->execute(['id' => $id]);
    $data = $rq->fetchAll();
    return $data;
}


function CreatePlageInstance($id, $plage, $km)
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `plage` where id_plages=:plage");
    $rq->execute(['plage' => $plage]);
    $plageresult = $rq->fetch();
    $rq = $pdo->prepare("INSERT INTO `instanceplages` ( `FK_id_etudes`, `FK_id_plages`, `superficieTotal`) VALUES ( :id, :plageid, :km)");
    $rq->execute(['id' => $id, 'plageid' => $plageresult["id_plages"], 'km' => $km]);
}


function SupprPlageInstance($id)
{
    global $pdo;
    $rq = $pdo->prepare(" DELETE FROM `instanceplages` WHERE `instanceplages`.`id_instancePlages` = :id");
    $rq->execute(['id' => $id]);
}


function addEspece($nom)
{
    global $pdo;
    $query = $pdo->prepare("INSERT INTO `especes` (`nom`) VALUES (:nom)");
    $query->execute(['nom' => $nom]);
}


function listeEspece()
{
    global $pdo;
    $query = $pdo->prepare("SELECT * FROM `especes`");
    $query->execute();
    $liste = $query->fetchAll();
    return $liste;
}


function deleteEspece($id_especes)
{
    global $pdo;
    $query = $pdo->prepare("DELETE FROM `especes` WHERE `id_especes`=:id_especes");
    $query->execute(['id_especes' => $id_especes]);
}


function modifyEspeces($id_especes, $nom){
    global $pdo;
    $query = $pdo->prepare("UPDATE `especes` SET `nom`=:nom WHERE id_especes=:id_especes");
    $query->execute(['id_especes' => $id_especes, 'nom' => $nom]);
}


function addPlage($nom, $commune, $departement){
    global $pdo;
    $query = $pdo->prepare("INSERT INTO `plage`(`nom`, `commune`, `departement`) VALUES (:nom, :commune, :departement)");
    $query->execute(['nom' => $nom, 'commune' => $commune, 'departement' => $departement]);
}


function listePlage(){
    global $pdo;
    $query = $pdo->prepare("SELECT * FROM `plage`");
    $query->execute();
    $row = $query->fetchAll();
    return $row;
}


function deletePlage($id_plages){
    global $pdo;
    $query = $pdo->prepare("DELETE FROM `plage` WHERE `id_plages`=:id_plages");
    $query->execute(['id_especes' => $id_plages]);
}


function modifyPlage($id_plages, $nom, $commune, $departement){
    global $pdo;
    $query = $pdo->prepare("UPDATE `plage` SET `nom`=:nom, `commune`=:commune, `departement`=:departement WHERE id_plages=:id_plages");
    $query->execute(['id_plages' => $id_plages, 'nom' => $nom, 'commune' => $commune, 'departement' => $departement]);
}


function selectModifyPlage($id_plages){
    global $pdo;
    $query = $pdo->prepare("SELECT `id_plages`, `nom`, `commune`, `departement` FROM `plage` WHERE id_plages=:id_plages");
    $query->execute([ 'id_plages' => $id_plages]);
    $onePlage = $query ->fetchAll();
    return $onePlage;
}


function selectModifyEspeces($id_especes){
    global $pdo;
    $query = $pdo->prepare("SELECT `id_especes`, `nom` FROM `especes` WHERE id_especes=:id_especes");
    $query->execute([ 'id_especes' => $id_especes]);
    $oneEspeces = $query ->fetchAll();
    return $oneEspeces;
}


function getOpenEtudes()
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `etudes` where dateFin is null ");
    $rq->execute();
    $data = $rq->fetchAll();
    return $data;

}


function getZones($id){
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `zones` WHERE `FK_instance_plages` = :id");
    $rq->execute(["id" => $id]);
    $data = $rq->fetchAll();
    return $data;

}


function getZonedetails($id)
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `zones` WHERE `id_zones`= :id");
    $rq->execute(["id" => $id]);
    $data = $rq->fetch();
    return $data;

}


function getInstEspece($id)
{
    global $pdo;
    $rq = $pdo->prepare("SELECT * FROM `instanceespeces` i JOIN especes e ON e.id_especes=i.FK_id_especes WHERE `FK_zone`=:id");
    $rq->execute(['id' => $id]);
    $data = $rq->fetchAll();
    return $data;
}


function deleteInstEspece($id_espece, $id_zone)
{
    global $pdo;
    $rq = $pdo->prepare("DELETE FROM instanceespeces WHERE FK_id_especes = :espece AND FK_zone = :zone");
    $rq->execute(['espece' => $id_espece, 'zone' => $id_zone]);
}


function addInstEspece($id_espece, $id_zone, $nombre)
{
    global $pdo;
    $rq = $pdo->prepare("INSERT INTO `instanceespeces` (`FK_id_especes`, `FK_zone`, `nombre`) VALUES (:espece, :zone, :nombre)");
    $rq->execute(['espece' => $id_espece, 'zone' => $id_zone, 'nombre' => $nombre]);
}


function createNewZone($id_plage,$nombrePersonne){
    global $pdo;
    $rq = $pdo->prepare("INSERT INTO zones(FK_instance_plages,nombrePersonne) VALUES (:FK_instance_plages,:nombrePersonne)");
    $rq->execute(['FK_instance_plages' => $id_plage, 'nombrePersonne' => $nombrePersonne,]);
}

