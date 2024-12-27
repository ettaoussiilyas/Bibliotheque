<?php

class Categoty{ 

    //attributes
    private $id;
    private $name;

    //connection

    private $conn;
    private $table_name = "categories";


    //erreur array
    public $errers = array();

    public function __construct($conn){

        $this->conn = $conn;

    }

    public function getAllCategorys(){
        
        $query = "SELECT * FROM ".$this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);    

        return $result;
    }

    //delete categorie
    public function deletecCategory($id){

        $query = "DELETE FROM ".$this->table_name." WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute(['id'=>$id]);
        return $result;
    }

    public function getCategoryById($id){

        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function addingCategory($data){

        if(strlen($data['name'])<3){
            $this->errers['name'] = 'Le nom de la catégorie ne peut pas être vide';
            return false;
        }

        $query = "INSERT INTO ".$this->table_name." Values(null , :name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name',$data['name']);
        $result = $stmt->execute(['name'=>$data['name']]);
        
        if(!$result){
            $this->errers['general'] = 'Erreur lors de l\'ajout de la catégorie';
            return false;
        }
        return true;

    }

    //editCategory

    public function editCategory($data){


        // if(strlen($data['name'])){
        //     $this->errers['name'] = 'Le nom de la catégorie ne peut pas être vide';
        //     return false;
        // }
        $query = "UPDATE ".$this->table_name." SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['name'=>$data['name'],'id'=>$data['id']]);

        return $stmt;
        
    }

    public function getErrorMessage(){
        return $this->errers;
    }


}



?>