{include file="sections/header.tpl"}

<!-- data_usage.tpl -->

<h2>Data Usage</h2>

<table>
  <thead>
    <tr>
      <th>Username</th>
      <th>Usage</th>
    </tr>
  </thead>
  <tbody>
    {foreach $dataUsage as $key => $response}
  {$key}: {$response->getProperty('p')}<br>
{/foreach}

      <tr>
        <td>{$.username}</td>
        <td>{$usage.usage}</td>
      </tr>
  </tbody>
</table>



{include file="sections/footer.tpl"}
