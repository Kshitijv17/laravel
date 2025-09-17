// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'user.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

User _$UserFromJson(Map<String, dynamic> json) => User(
  id: (json['id'] as num).toInt(),
  name: json['name'] as String,
  email: json['email'] as String,
  phone: json['phone'] as String?,
  profileImage: json['profile_image'] as String?,
  emailVerifiedAt: json['email_verified_at'] as String?,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$UserToJson(User instance) => <String, dynamic>{
  'id': instance.id,
  'name': instance.name,
  'email': instance.email,
  'phone': instance.phone,
  'profile_image': instance.profileImage,
  'email_verified_at': instance.emailVerifiedAt,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};

Admin _$AdminFromJson(Map<String, dynamic> json) => Admin(
  id: (json['id'] as num).toInt(),
  name: json['name'] as String,
  email: json['email'] as String,
  phone: json['phone'] as String?,
  emailVerifiedAt: json['email_verified_at'] as String?,
  avatar: json['avatar'] as String?,
  status: json['status'] as bool,
  permissions: (json['permissions'] as List<dynamic>)
      .map((e) => e as String)
      .toList(),
  isActive: json['is_active'] as bool,
  lastLoginAt: json['last_login_at'] as String?,
  lastLoginIp: json['last_login_ip'] as String?,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$AdminToJson(Admin instance) => <String, dynamic>{
  'id': instance.id,
  'name': instance.name,
  'email': instance.email,
  'phone': instance.phone,
  'email_verified_at': instance.emailVerifiedAt,
  'avatar': instance.avatar,
  'status': instance.status,
  'permissions': instance.permissions,
  'is_active': instance.isActive,
  'last_login_at': instance.lastLoginAt,
  'last_login_ip': instance.lastLoginIp,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};

AuthResponse _$AuthResponseFromJson(Map<String, dynamic> json) => AuthResponse(
  token: json['token'] as String,
  admin: json['admin'] == null
      ? null
      : Admin.fromJson(json['admin'] as Map<String, dynamic>),
  user: json['user'] == null
      ? null
      : User.fromJson(json['user'] as Map<String, dynamic>),
);

Map<String, dynamic> _$AuthResponseToJson(AuthResponse instance) =>
    <String, dynamic>{
      'token': instance.token,
      'admin': instance.admin,
      'user': instance.user,
    };
