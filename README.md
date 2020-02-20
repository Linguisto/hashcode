## Google HashCode PHP Framework

This is a simple solution for Google Hashcode competitors which prefer PHP, especially laravel. This framework is
 based on the [Laravel Zero](https://github.com/laravel-zero/laravel-zero) framework. 
 
 It utilizes the core HashCode operations letting the competitors concentrate on the solution of the problem.

## Usage

### Solvers
Solvers are basically classes that can provide a solution of your current problem on HashCode. Solution result is
 always an array that is written into an `.out` file line-by-line, values divided by spaces. 
 
In order to create a solver, create a class, for example, `MySolver` under the `App\Solvers` namespace and implement 
the `ProvidesSolution` contract. Optionally you can implement the `CalculatesPreliminaryScore` contract so that 
the output will also have a preliminary calculated score of your solution. 

### CLI

```$xslt
php hashcode run {filepath} {solver}
```   

This command accepts 2 parameters:

`filepath` - the path to your `.in` file

`solver` - your solver name. E.g. for `MySolver` class this parameter should be `my`. 

##### Example usage for MySolver class

```$xslt
php hashcode run /path/to/file.in my
``` 

## License

Hashcode PHP Framework inherits its licence from Laravel Zero project, which is an open-source software licensed 
under the [MIT license](https://github.com/laravel-zero/laravel-zero/blob/stable/LICENSE.md).
