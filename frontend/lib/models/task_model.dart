class Task {
  final int id;
  final String title;
  final bool isCompleted;
  final String createdAt;

  Task({
    required this.id,
    required this.title,
    required this.isCompleted,
    required this.createdAt,
  });

  factory Task.fromJson(Map<String, dynamic> json) {
    return Task(
      id: json['id'] ?? 0,
      title: json['title'] ?? 'Untitled',
      // SQLite/MySQL might return 0/1 for tinyint or boolean
      isCompleted: json['is_completed'] == 1 || json['is_completed'] == true || json['is_completed'] == '1',
      createdAt: json['created_at'] ?? DateTime.now().toIso8601String(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'is_completed': isCompleted,
      'created_at': createdAt,
    };
  }
}
