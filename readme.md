WHAT IS IT?
----
This cli tool will generate a DTO class to be used as a replacement for array.

WHY?
----
A lot of php code is array-heavy, the idea is to use objects for data transfer, especially as return types.

HOW TO USE?
----------
`$ php generate.php generate:dto-array %path% %className%` - a DTO %className% will be generated at %path%. 
You will be asked about the properties you want to include in the class.

TODO: 
-----
- Check that directory exist before writing generated classes
- Refactor GenerateArrayClassCommand's callback actions to standalone classes
- Add tests for ArrayClassGenerator (introduce filesystem writer dependency)