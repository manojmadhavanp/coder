<?php
// This is the main content for the dashboard.
// It will be rendered within a layout template (e.g., admin.tpl or superadmin.tpl)
// by the System\HTTP\Page class.
// Variables like $page_title and $firstName are passed from the Page::content() method.
?>

<h1><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Manager Dashboard'; ?></h1>

<p>Hello, <?php echo isset($firstName) ? htmlspecialchars($firstName) : 'User'; ?>!</p>

<p>This is your protected dashboard area. You can manage various aspects of the site from here.</p>

<p><a href="/manage/logout">Logout</a></p>

<div>
    <h2>Quick Links</h2>
    <ul>
        <li><a href="#">Manage Users (Placeholder)</a></li>
        <li><a href="#">Site Settings (Placeholder)</a></li>
        <li><a href="#">View Reports (Placeholder)</a></li>
    </ul>
</div>

<p>Further dashboard content and widgets will go here.</p>
