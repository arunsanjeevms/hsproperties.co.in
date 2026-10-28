<?php
/**
 * HS Properties — Property Controller.
 *
 * Handles admin CRUD operations for portfolio and listed properties.
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../models/Property.php';

class PropertyController
{
    private Property $model;

    public function __construct()
    {
        $this->model = new Property();
    }

    /**
     * Store a newly created property.
     */
    public function store(): void
    {
        Auth::requireAdmin();

        $validator = new Validator($_POST);
        $validator->required('title', 'Property title is required.');
        $validator->required('location', 'Location is required.');
        $validator->required('type', 'Property type is required.');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $_POST);
            redirect(url('admin/properties.php?action=create'));
        }

        $imagePath = 'assets/images/property-sandstone-pavilion.jpg';
        if (!empty($_FILES['image']['name'])) {
            $uploader = new FileUpload(APP_ROOT . '/uploads/properties');
            $file = $uploader->uploadSingle($_FILES['image']);
            if ($file) {
                $imagePath = 'uploads/properties/' . basename($file['path']);
            }
        }

        $id = $this->model->create([
            'reference'    => $_POST['reference'] ?? ('#' . strtoupper(substr(uniqid(), -6))),
            'title'        => trim($_POST['title']),
            'location'     => trim($_POST['location']),
            'type'         => $_POST['type'],
            'status'       => $_POST['status'] ?? 'Active',
            'mandate'      => trim($_POST['mandate'] ?? 'Exclusive Advisory'),
            'spec'         => trim($_POST['spec'] ?? ''),
            'summary'      => trim($_POST['summary'] ?? ''),
            'image'        => $imagePath,
            'image_alt'    => trim($_POST['image_alt'] ?? $_POST['title']),
            'is_portfolio' => isset($_POST['is_portfolio']) ? 1 : 0,
            'is_active'    => 1,
            'sort_order'   => (int)($_POST['sort_order'] ?? 0),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        log_activity('property_created', 'properties', $id, "Added property {$_POST['title']}");
        Session::flash('success', 'Property successfully created.');
        redirect(url('admin/properties.php'));
    }

    /**
     * Update an existing property.
     */
    public function update(int $id): void
    {
        Auth::requireAdmin();

        $validator = new Validator($_POST);
        $validator->required('title', 'Property title is required.');
        $validator->required('location', 'Location is required.');

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            redirect(url('admin/properties.php?action=edit&id=' . $id));
        }

        $data = [
            'reference'    => $_POST['reference'] ?? '',
            'title'        => trim($_POST['title']),
            'location'     => trim($_POST['location']),
            'type'         => $_POST['type'] ?? 'Estate',
            'status'       => $_POST['status'] ?? 'Active',
            'mandate'      => trim($_POST['mandate'] ?? ''),
            'spec'         => trim($_POST['spec'] ?? ''),
            'summary'      => trim($_POST['summary'] ?? ''),
            'is_portfolio' => isset($_POST['is_portfolio']) ? 1 : 0,
            'sort_order'   => (int)($_POST['sort_order'] ?? 0),
        ];

        if (!empty($_FILES['image']['name'])) {
            $uploader = new FileUpload(APP_ROOT . '/uploads/properties');
            $file = $uploader->uploadSingle($_FILES['image']);
            if ($file) {
                $data['image'] = 'uploads/properties/' . basename($file['path']);
            }
        }

        $this->model->update($id, $data);
        log_activity('property_updated', 'properties', $id, "Updated property {$data['title']}");
        Session::flash('success', 'Property updated successfully.');
        redirect(url('admin/properties.php'));
    }

    /**
     * Delete a property.
     */
    public function destroy(int $id): void
    {
        Auth::requireAdmin();
        $this->model->delete($id);
        log_activity('property_deleted', 'properties', $id, "Deleted property #{$id}");
        Session::flash('success', 'Property deleted.');
        redirect(url('admin/properties.php'));
    }
}
