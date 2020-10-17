Feature:
  Check hello world page

  Scenario: I receive a Hello World response from hello page
    When I call the Hello page
    Then It should say hello to "world"
