<?php

class M_user extends CI_Model
{

    public function get_all_pegawai()
{
    $hasil = $this->db->query('
        SELECT user.*, user_detail.*, jenis_kelamin.jenis_kelamin, department.nama_department
        FROM user 
        JOIN user_detail ON user.id_user_detail = user_detail.id_user_detail 
        JOIN jenis_kelamin ON user_detail.id_jenis_kelamin = jenis_kelamin.id_jenis_kelamin 
        LEFT JOIN department ON user.id_department = department.id_department
        WHERE user.id_user_level = 1 
        ORDER BY user.username ASC
    ');
    
    return $hasil->result_array(); // Kembalikan hasil sebagai array
}

    
    // public function get_all_pegawai()
    // {
    //     $this->db->select('u.id_user, u.username, d.nama_departemen'); // Ambil nama departemen
    //     $this->db->from('user u'); // Gunakan tabel user
    //     $this->db->join('departemen d', 'u.id_department = d.id_department', 'left'); // Lakukan join ke tabel departemen
    //     $query = $this->db->get();
    //     return $query->result_array(); // Pastikan hasilnya berupa array
    // }
    

    public function count_all_pegawai()
    {
        $hasil = $this->db->query('SELECT COUNT(id_user) as total_user FROM user JOIN user_detail ON user.id_user_detail = user_detail.id_user_detail 
        JOIN jenis_kelamin ON user_detail.id_jenis_kelamin = jenis_kelamin.id_jenis_kelamin 
        WHERE id_user_level = 1');
        return $hasil;
    }

    public function count_all_admin()
    {
        $hasil = $this->db->query('SELECT COUNT(id_user) as total_user FROM user
        WHERE id_user_level = 2');
        return $hasil;
    }

    public function get_all_admin()
    {
        $hasil = $this->db->query('SELECT * FROM user
        WHERE id_user_level = 2');
        return $hasil;
    }

    public function get_pegawai_by_id($id_user)
    {
        $hasil = $this->db->query("SELECT * FROM user JOIN user_detail ON user.id_user_detail = user_detail.id_user_detail 
        WHERE user.id_user='$id_user'");
        return $hasil;
    }

    public function cek_login($username)
    {

        $hasil = $this->db->query("SELECT * FROM user JOIN user_detail ON user.id_user_detail = user_detail.id_user_detail WHERE username='$username'");
        return $hasil;

    }

    public function pendaftaran_user($id, $username, $email, $password, $id_user_level)
    {
        $this->db->trans_start();

        $this->db->query("INSERT INTO user(id_user,username,password,email,id_user_level, id_user_detail) VALUES ('$id','$username','$password','$email','$id_user_level','$id')");
        $this->db->query("INSERT INTO user_detail(id_user_detail) VALUES ('$id')");

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
            return true;
        else
            return false;
    }

    public function update_user_detail($id, $nama_lengkap, $id_jenis_kelamin, $no_telp, $alamat, $nip, $id_department, $jabatan)
{
    $this->db->trans_start();

    $data = [
        'nama_lengkap' => $nama_lengkap,
        'id_jenis_kelamin' => $id_jenis_kelamin,
        'no_telp' => $no_telp,
        'alamat' => $alamat,
        'nip' => $nip,
        'id_department' => $id_department, // Perbaiki nama kolom jika diperlukan
        'jabatan' => $jabatan,
    ];

    $this->db->where('id_user_detail', $id);
    $this->db->update('user_detail', $data);

    $this->db->trans_complete();
    return $this->db->trans_status();
}

public function insert_pegawai($id, $username, $email, $password, $id_user_level, $nama_lengkap, $id_jenis_kelamin, $no_telp, $alamat, $id_department)
{
    $this->db->trans_start();

    // Insert into user
    $this->db->insert('user', [
        'id_user' => $id,
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'id_user_level' => $id_user_level,
        'id_user_detail' => $id,
        'id_department' => $id_department,
    ]);

    // Insert into user_detail
    $this->db->insert('user_detail', [
        'id_user_detail' => $id,
        'nama_lengkap' => $nama_lengkap,
        'id_jenis_kelamin' => $id_jenis_kelamin,
        'no_telp' => $no_telp,
        'alamat' => $alamat,
        'id_department' => $id_department, // Pastikan nama kolom ini benar
    ]);

    $this->db->trans_complete();

    return $this->db->trans_status();
}

public function update_pegawai($id, $username, $email, $password, $id_user_level, $nama_lengkap, $id_jenis_kelamin, $no_telp, $alamat, $id_department)
{
    $this->db->trans_start();

    // Update user
    $this->db->where('id_user', $id); // Pastikan $id adalah id_user
    $this->db->update('user', [
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'id_user_level' => $id_user_level,
        'id_department' => $id_department, // Update id_department jika diperlukan
    ]);

    // Update user_detail
    $this->db->where('id_user_detail', $id); // Pastikan $id adalah id_user_detail
    $this->db->update('user_detail', [
        'nama_lengkap' => $nama_lengkap,
        'id_jenis_kelamin' => $id_jenis_kelamin,
        'no_telp' => $no_telp,
        'alamat' => $alamat,
        'id_department' => $id_department, // Pastikan nama kolom ini benar
    ]);

    $this->db->trans_complete();

    return $this->db->trans_status();
}



    public function delete_pegawai($id)
    {
        $this->db->trans_start();

        $this->db->query("DELETE FROM user WHERE id_user='$id'");
        $this->db->query("DELETE FROM user_detail WHERE id_user_detail='$id'");

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
            return true;
        else
            return false;
    }

    public function update_user($id, $username, $password)
    {
        $this->db->trans_start();

        $this->db->query("UPDATE user SET username='$username', password='$password' WHERE id_user='$id'");

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
            return true;
        else
            return false;
    }



    public function delete_admin($id)
    {
        $this->db->trans_start();

        $this->db->query("DELETE FROM user WHERE id_user='$id'");
        $this->db->query("DELETE FROM user_detail WHERE id_user_detail='$id'");

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
            return true;
        else
            return false;
    }

}