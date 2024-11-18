<?php
namespace Controllers;

use Models\Employee;
use Exception;

class EmployeeController {
    public function getEmployees($filters = []) {
        try {
            // Logic lấy danh sách nhân viên với filter
            return [
                'success' => true,
                'data' => [] // Danh sách nhân viên
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createEmployee($data) {
        try {
            // Validate dữ liệu
            if (empty($data['username']) || empty($data['password'])) {
                throw new Exception("Thiếu thông tin tài khoản");
            }

            // Tạo nhân viên mới
            $employee = new Employee($data);
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Tạo tài khoản nhân viên thành công',
                'data' => $employee->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateEmployee($employeeId, $data) {
        try {
            $employee = new Employee(['id' => $employeeId]);
            
            // Cập nhật thông tin
            if (isset($data['fullName'])) {
                $employee->setFullName($data['fullName']);
            }
            if (isset($data['salary'])) {
                $employee->setSalary($data['salary']);
            }
            // Cập nhật các trường khác
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $employee->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 