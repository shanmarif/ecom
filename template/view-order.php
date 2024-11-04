<div class="wrap container-fluid" style="margin-bottom:10px;">
    <h2>View Order Details</h2>
</div>
<?php
    $entry = $context["order_details"][0];
    $aAction = isset($context['action']) && !empty($context['action']) ? $context['action'] : 'view_order_details';
?>
<div class="wrap container-fluid">

    <style>
        /* Custom styles for detailed view */
        .details-section {
            margin-bottom: 30px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #0073aa;
        }
        .details-section h2 {
            margin-top: 0;
            font-size: 18px;
            font-weight: 600;
            color: #0073aa;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .details-section table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details-section th {
            text-align: left;
            padding: 10px;
            background-color: #f9f9f9;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }
        .details-section td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .back-button {
            background-color: #0073aa;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #005c8c;
        }
        .addon-list ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        .addon-list li {
            margin-bottom: 10px;
        }
        .logo-image {
            max-width: 200px;
            height: auto;
            display: block;
        }
        .download-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: white; /* Change background to white */
            color: black; /* Change text color to black */
            text-decoration: none;
            border-radius: 4px;
 margin-top: 10px;
            border: 1px solid #ccc; /* Optional: Add border for better visibility */
        }
        .download-button:focus,
        .download-button:active {
            color: black; /* Keep text color black */
            outline: none; /* Remove outline if desired */
        }
    </style>

    <!-- Basic Info Section -->
    <div class="details-section">
        <h2>Basic Information</h2>
        <table>
            <tr>
                <th>Name</th>
                <td><?php echo esc_html($entry['name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo esc_html($entry['email']); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo esc_html($entry['phone_number']); ?></td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td><?php echo esc_html($entry['payment_method']); ?></td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>$<?php echo esc_html($entry['grand_total']); ?></td>
            </tr>
        </table>
    </div>

    <!-- Trademark Info Section -->
    <div class="details-section">
        <h2>Trademark Information</h2>
        <table>
            <tr>
                <th>Trademark Type</th>
                <td><?php echo esc_html($entry['trademark_type']); ?></td>
            </tr>
            <tr>
                <th>Trademark Text</th>
                <td><?php echo esc_html($entry['trademark_text']); ?></td>
            </tr>
            <tr>
                <th>Logo</th>
                <td>
                    <?php
                    // Check if the logo file is uploaded
                    if (!empty($entry['logo_file'])) {
                        // Get the logo image HTML
                        echo wp_get_attachment_image($entry['logo_file'], 'medium', false, array('class' => 'logo-image'));
                        
                        // Get the logo URL for downloading
                        $logo_url = wp_get_attachment_url($entry['logo_file']);
                        
                        // Provide a download button
                        echo '<br><a href="' . esc_url($logo_url) . '" download class="download-button">Download Logo</a>';
                    } else {
                        // Display "Image not uploaded" text if no logo is uploaded
                        echo 'Image not uploaded';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Trademark In Use</th>
                <td><?php echo $entry['trademark_inuse'] == 'Y' ? 'Yes' : 'No'; ?></td>
            </tr>
            <tr>
                <th>Trademark Date</th>
                <td><?php echo $entry['trademark_date'] == '0000-00-00' ? 'N/A' : esc_html($entry['trademark_date']); ?></td>
            </tr>
        </table>
    </div>

    <!-- Package and Addons Section -->
    <div class="details-section addon-list">
        <h2>Package & Addons</h2>
        <table>
            <tr>
                <th>Package Name</th>
                <td><?php echo esc_html($entry['package_name']); ?></td>
            </tr>
            <tr>
                <th>Addons</th>
                <td>
                    <ul>
                        <?php foreach ($entry['addon_details'] as $addon): ?>
                            <li>
                                <?php echo esc_html($addon['name']); ?> 
                                (Class <?php echo esc_html($addon['classification']); ?>) 
                                <br>
                                <small><?php echo esc_html($addon['description']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <!-- Additional Notes Section -->
    <div class="details-section">
        <h2>Additional Notes</h2>
        <p><?php echo esc_html($entry['additional_notes']); ?></p>
    </div>

    <a href="admin.php?page=list_orders" class="back-button">Back to Orders</a>
</div>