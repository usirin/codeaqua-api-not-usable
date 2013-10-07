# A sample Guardfile
# More info at https://github.com/guard/guard#readme

guard 'phpunit', :cli => '--colors', :test_path => 'app/tests' do
  watch(%r{^.+Test\.php$})

  watch(%r{app/(.+)/(.+).php}) { |m| "tests/#{m[1]}/#{m[ 2]}Test.php"}
end
