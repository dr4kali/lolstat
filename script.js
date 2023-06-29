function getChampionName(championId) {
    const url = 'http://ddragon.leagueoflegends.com/cdn/6.24.1/data/en_US/champion.json';
  
    return fetch(url)
      .then(response => response.json())
      .then(data => {
        const championData = Object.values(data.data);
        const champion = championData.find(champ => champ.key === String(championId));
  
        if (champion) {
          return champion.name;
        } else {
          throw new Error('Champion not found.');
        }
      })
      .catch(error => {
        console.log('Error:', error);
        throw new Error('An error occurred.');
      });
  }
  