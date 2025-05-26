// ... existing code ...

public function getTotalElections() {
    $query = "SELECT COUNT(*) as total FROM elecciones";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}

// ... existing code ...