<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    // ─── Posts CRUD ──────────────────────────────────────────────────

    public function index(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $posts = BlogPost::getAll();

        $this->view('admin/blog/index', [
            'title' => 'Administrar Blog',
            'posts' => $posts,
        ], 'admin');
    }

    public function create(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $categories = BlogCategory::getAll();

        $this->view('admin/blog/create', [
            'title'      => 'Nuevo Artículo',
            'categories' => $categories,
        ], 'admin');
    }

    public function store(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $title   = $_POST['title'] ?? '';
        $slug    = !empty($_POST['slug']) ? $_POST['slug'] : BlogPost::generateSlug($title);
        $excerpt = $_POST['excerpt'] ?? '';
        $content = $_POST['content'] ?? '';
        $image   = $_POST['featured_image'] ?? '';
        $published = isset($_POST['published']) ? 1 : 0;
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        // SEO fields
        $metaTitle       = $_POST['meta_title'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $jsonLd          = $_POST['json_ld'] ?? '';

        // Ensure unique slug
        $existing = BlogPost::findBySlug($slug);
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        BlogPost::createPost([
            'title'            => $title,
            'slug'             => $slug,
            'excerpt'          => $excerpt,
            'content'          => $content,
            'featured_image'   => $image,
            'published'        => $published,
            'category_id'      => $categoryId,
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'json_ld'          => $jsonLd,
        ]);

        $this->redirect(BASE_URL . '/admin/blog');
    }

    public function edit(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $post = BlogPost::findById($id);

        if (!$post) {
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $categories = BlogCategory::getAll();

        $this->view('admin/blog/edit', [
            'title'      => 'Editar Artículo',
            'post'       => $post,
            'categories' => $categories,
        ], 'admin');
    }

    public function update(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $title   = $_POST['title'] ?? '';
        $slug    = !empty($_POST['slug']) ? $_POST['slug'] : BlogPost::generateSlug($title);
        $excerpt = $_POST['excerpt'] ?? '';
        $content = $_POST['content'] ?? '';
        $image   = $_POST['featured_image'] ?? '';
        $published = isset($_POST['published']) ? 1 : 0;
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        // SEO fields
        $metaTitle       = $_POST['meta_title'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $jsonLd          = $_POST['json_ld'] ?? '';

        BlogPost::updatePost($id, [
            'title'            => $title,
            'slug'             => $slug,
            'excerpt'          => $excerpt,
            'content'          => $content,
            'featured_image'   => $image,
            'published'        => $published,
            'category_id'      => $categoryId,
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'json_ld'          => $jsonLd,
        ]);

        $this->redirect(BASE_URL . '/admin/blog');
    }

    public function destroy(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        BlogPost::deletePost($id);
        $this->redirect(BASE_URL . '/admin/blog');
    }

    // ─── Categories CRUD ─────────────────────────────────────────────

    public function categories(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $categories = BlogCategory::getAllWithCount();

        $this->view('admin/blog/categories/index', [
            'title'      => 'Categorías del Blog',
            'categories' => $categories,
        ], 'admin');
    }

    public function createCategory(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $this->view('admin/blog/categories/create', [
            'title' => 'Nueva Categoría',
        ], 'admin');
    }

    public function storeCategory(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $name        = $_POST['name'] ?? '';
        $slug        = !empty($_POST['slug']) ? $_POST['slug'] : BlogCategory::generateSlug($name);
        $description = $_POST['description'] ?? '';

        // Ensure unique slug
        $existing = BlogCategory::findBySlug($slug);
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        BlogCategory::createCategory([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
        ]);

        $this->redirect(BASE_URL . '/admin/blog/categorias');
    }

    public function editCategory(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $category = BlogCategory::findById($id);

        if (!$category) {
            $this->redirect(BASE_URL . '/admin/blog/categorias');
        }

        $this->view('admin/blog/categories/edit', [
            'title'    => 'Editar Categoría',
            'category' => $category,
        ], 'admin');
    }

    public function updateCategory(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $name        = $_POST['name'] ?? '';
        $slug        = !empty($_POST['slug']) ? $_POST['slug'] : BlogCategory::generateSlug($name);
        $description = $_POST['description'] ?? '';

        // Check slug uniqueness (exclude current)
        $existing = BlogCategory::findBySlug($slug);
        if ($existing && $existing['id'] !== $id) {
            $slug = $slug . '-' . time();
        }

        BlogCategory::updateCategory($id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
        ]);

        $this->redirect(BASE_URL . '/admin/blog/categorias');
    }

    public function destroyCategory(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        BlogCategory::deleteCategory($id);
        $this->redirect(BASE_URL . '/admin/blog/categorias');
    }
}
