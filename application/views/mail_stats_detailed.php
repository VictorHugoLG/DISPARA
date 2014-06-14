<div class="row" style="margin-left :0.1%">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Campanha</th>
                <th>Email</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats as $mail) : ?>
                <tr>
                    <td><?php echo $mail->mail_data_id; ?></td>
                    <td><?php echo $mail->prefix.'@'.$mail->domain; ?></td>
                    <td><?php echo $mail->status; ?></td>
                    <td>
                        <?php echo substr($mail->dttm_changed, 8, 2).'/'.substr($mail->dttm_changed, 5, 2).'/'.substr($mail->dttm_changed, 0, 4); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>