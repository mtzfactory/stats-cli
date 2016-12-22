import { StatsTestPage } from './app.po';

describe('stats-test App', function() {
  let page: StatsTestPage;

  beforeEach(() => {
    page = new StatsTestPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
