# Notes

- It is probably less efficient to render a child block in `getHxRequest($childBlock)` compared to rendering it split up
  in various top level components

- Perhaps a serialize / deserialize component method
  this way i can just append the serialized component to the rest of the request

- must be able to talk to different controllers to perform actions
