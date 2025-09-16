import 'package:json_annotation/json_annotation.dart';

part 'user.g.dart';

@JsonSerializable()
class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  @JsonKey(name: 'profile_image')
  final String? profileImage;
  @JsonKey(name: 'email_verified_at')
  final String? emailVerifiedAt;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.profileImage,
    this.emailVerifiedAt,
    required this.createdAt,
    required this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) => _$UserFromJson(json);
  Map<String, dynamic> toJson() => _$UserToJson(this);
}

@JsonSerializable()
class Admin {
  final int id;
  final String name;
  final String email;
  final String? phone;
  @JsonKey(name: 'email_verified_at')
  final String? emailVerifiedAt;
  final String? avatar;
  final bool status;
  final List<String> permissions;
  @JsonKey(name: 'is_active')
  final bool isActive;
  @JsonKey(name: 'last_login_at')
  final String? lastLoginAt;
  @JsonKey(name: 'last_login_ip')
  final String? lastLoginIp;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Admin({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.emailVerifiedAt,
    this.avatar,
    required this.status,
    required this.permissions,
    required this.isActive,
    this.lastLoginAt,
    this.lastLoginIp,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Admin.fromJson(Map<String, dynamic> json) => _$AdminFromJson(json);
  Map<String, dynamic> toJson() => _$AdminToJson(this);
}

@JsonSerializable()
class AuthResponse {
  final String token;
  final Admin? admin;
  final User? user;

  AuthResponse({
    required this.token,
    this.admin,
    this.user,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) => _$AuthResponseFromJson(json);
  Map<String, dynamic> toJson() => _$AuthResponseToJson(this);
}
