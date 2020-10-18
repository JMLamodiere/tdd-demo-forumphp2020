Feature:
  Running session

  Scenario: The running session I register is enriched with current weather data
    Given current temperature is "37.5" celcius degrees
    When I register a running session with id "15" distance "25.7" and shoes "black shoes"
    Then a running session should be added with id "15" distance "25.7" shoes "black shoes" and temperature "37.5"
