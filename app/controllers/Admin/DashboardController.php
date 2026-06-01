<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\Service;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function index(): void
    {
        // Auth check
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $blogCount      = count(BlogPost::getAll());
        $serviceCount   = count(Service::getAll());
        $contactCount   = count(Contact::getAll());
        $unreadContacts = Contact::getUnreadCount();

        $this->view('admin/dashboard', [
            'title'          => 'Dashboard',
            'blogCount'      => $blogCount,
            'serviceCount'   => $serviceCount,
            'contactCount'   => $contactCount,
            'unreadContacts' => $unreadContacts,
        ], 'admin');
    }
}
