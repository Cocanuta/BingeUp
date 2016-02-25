<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
    <h2>Users</h2>
    <table class ="table table-bordered">
        <thread>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>E-Mail</th>
                <th>Joined</th>
                <th>User Level</th>
                <th>Confirmed</th>
            </tr>
        </thread>
        <tbody>
            <?php foreach($users->result() as $row): ?>
                <tr>
                    <td><?php echo $row->id; ?></td>
                    <td><?php echo $row->username; ?></td>
                    <td><?php echo $row->email; ?></td>
                    <td><?php echo $row->created_at; ?></td>
                    <td><?php echo $row->is_admin; ?></td>
                    <td><?php echo $row->is_confirmed; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
