Introduction
=====

Use csschecker to perform checks on your CSS and its interaction with other source files in your project.


Usage
=====

Example: 

    csschecker --verbose --config myProject/myRules.json myProject/source/ myProject/web/css

Options:

    --config                Relative or absolute path to a ruleset.json file
    --verbose               Enable verbose output

Providing multiple code or source folders:

    --additionalCodeDir     Additional source code folder to scan. Option can be used multiple times
    --additionalCssDir      Additional css code folder to scan. Option can be used multiple times
