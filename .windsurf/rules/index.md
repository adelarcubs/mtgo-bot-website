---
trigger: manual
---

# About this project

- This is a mtgo bot frontend using mezzio
- The code is PHP
- When some coded is needed assume that you are a senior PHP developer
- Tempalte render is TWIG

# Directory Structure
```text
src/
├── App/
│   ├── Entity/
│   │   └── ExampleEntity.php
│   ├── Handler/
│   │   └── Api/
│   │       └── ExampleHandler.php
│   ├── Repository/
│   │   └── ExampleRepository.php
```

# Rules
- Don't change files in vendor directory
- Before any commit run ```composer cs-fix```
- When commit add in message that this commit is execute using vibe code

## Container Rules
- 'doctrine' is not a valid container service, use EntityManagerInterface::class instead

## Entity Rules
- All Entity classes must use attributes to define properties for Doctrine ORM
- All Entity classes must use attributes to define relationships for Doctrine ORM
- All Entity classes must use attributes to define table name for Doctrine ORM
- All Entity classes must use attributes to define repository class for Doctrine ORM

## Entity Repository Rules
- Do not use ServiceEntityRepository class for Repositories, use EntityManagerInterface instead

## Factory Rules
- All Repository and Handler classes must have a factory
Always create variables for container services