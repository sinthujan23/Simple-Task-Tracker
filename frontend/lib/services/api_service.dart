import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/task_model.dart';

class ApiService {
  // Determine correct base URL based on environment
  static String get baseUrl {
    if (kIsWeb) {
      return 'http://localhost:8000/api/task';
    }
    // Only access Platform if NOT web
    try {
      if (Platform.isAndroid) {
        return 'http://10.0.2.2:8000/api/task';
      }
    } catch (e) {
      // Fallback for platforms where Platform.isAndroid might throw despite !kIsWeb check
      // (though rare in standard Flutter mobile/desktop)
    }
    return 'http://localhost:8000/api/task';
  }

  // HEADERS
  static const Map<String, String> headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  // GET TASKS
  Future<List<Task>> getTasks() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/list'), headers: headers);

      if (response.statusCode == 200) {
        final Map<String, dynamic> body = jsonDecode(response.body);
        if (body['status'] == true) {
          final List<dynamic> data = body['data'];
          return data.map((json) => Task.fromJson(json)).toList();
        } else {
          throw Exception(body['message']);
        }
      } else {
        throw Exception('Failed to load tasks: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error connecting to server: $e');
    }
  }

  // CREATE TASK
  Future<void> createTask(String title) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/create'),
        headers: headers,
        body: jsonEncode({'title': title}),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final Map<String, dynamic> body = jsonDecode(response.body);
        if (body['status'] != true) {
          throw Exception(body['message']);
        }
      } else {
        throw Exception('Failed to create task: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error creating task: $e');
    }
  }

  // COMPLETE TASK
  Future<void> completeTask(int id) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/complete/$id'),
        headers: headers,
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> body = jsonDecode(response.body);
        if (body['status'] != true) {
          throw Exception(body['message']);
        }
      } else {
        throw Exception('Failed to complete task: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error completing task: $e');
    }
  }
}
